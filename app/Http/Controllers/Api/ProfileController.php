<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{


    // Update profile details
   public function updateProfile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please login first to update profile.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:male,female,other',
            'phone' => 'sometimes|string|max:20',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'gender', 'phone']);

        if ($request->hasFile('profile_image')) {
            Log::info('Profile image upload started for user ID: ' . $user->id);

            $file = $request->file('profile_image');
            Log::info('File details:', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            try {
                // Delete old image - FIXED HERE
                if ($user->profile_picture) {
                    // Remove any '/storage/' or 'storage/' prefix
                    $oldImagePath = preg_replace('/^\/?storage\//', '', $user->profile_picture);
                    
                    Log::info('Attempting to delete old image: ' . $oldImagePath);
                    Log::info('Full old path in DB: ' . $user->profile_picture);
                    
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                        Log::info('Old image deleted successfully: ' . $oldImagePath);
                    } else {
                        Log::warning('Old image not found in storage: ' . $oldImagePath);
                        
                        // Try alternative path without 'profiles/' folder
                        $altPath = str_replace('profiles/', '', $oldImagePath);
                        if (Storage::disk('public')->exists($altPath)) {
                            Storage::disk('public')->delete($altPath);
                            Log::info('Old image deleted from alternative path: ' . $altPath);
                        }
                    }
                }

                // Generate unique filename
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store image - 'profiles' folder में
                $imagePath = $file->storeAs('profiles', $filename, 'public');
                Log::info('Image stored at path: ' . $imagePath); // 'profiles/filename.jpg'

                // Verify file exists
                if (Storage::disk('public')->exists($imagePath)) {
                    Log::info('File verified to exist in storage');
                    
                    // Get file info for logging
                    $fileSize = Storage::disk('public')->size($imagePath);
                    $fileLastModified = Storage::disk('public')->lastModified($imagePath);
                    Log::info('File info - Size: ' . $fileSize . ' bytes, Modified: ' . date('Y-m-d H:i:s', $fileLastModified));
                } else {
                    Log::error('File not found after storage: ' . $imagePath);
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to store image'
                    ], 500);
                }

                // FIXED: Store relative path in database (without '/storage/')
                // ये 'profiles/filename.jpg' form में store होगा
                $data['profile_picture'] = $imagePath;
                
                // For response, we'll use Storage::url() 
                $imageUrl = Storage::url($imagePath);
                Log::info('Image URL for response: ' . $imageUrl);

            } catch (\Exception $e) {
                Log::error('Image upload error: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to upload image: ' . $e->getMessage()
                ], 500);
            }
        }

        // Update user data
        $user->update($data);
        
        // Refresh user to get updated data
        $user->refresh();
        
        // Prepare response with full URL
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'gender' => $user->gender,
            'phone' => $user->phone,
            'email' => $user->email,
            'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
            'profile_picture_raw' => $user->profile_picture, // For debugging
            'updated_at' => $user->updated_at
        ];

        Log::info('Profile updated successfully for user ID: ' . $user->id);
        Log::info('User data after update:', $userData);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'user' => $userData
        ], 200);
    }


    // Get profile details  
    public function getProfile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'status' => true,
                'message' => 'Profile retrieved successfully',
                'user' => $user ,
                'user_type' => $user->is_admin ? 'admin' : 'user',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function switchRole(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please login first to switch role.'
            ], 401);
        }

        // Only passenger or driver can switch
        if (!in_array($user->user_type, ['passenger', 'driver'])) {
            return response()->json([
                'status' => false,
                'message' => 'Only passenger or driver accounts can switch roles.'
            ], 403);
        }

        // Switching logic
        $newRole = $user->user_type === 'passenger' ? 'driver' : 'passenger';

        $user->update(['user_type' => $newRole]);

        return response()->json([
            'status' => true,
            'message' => 'Role switched successfully.',
            'new_role' => $newRole,
            'user' => $user
        ], 200);
    }

}
