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
            Log::info('Profile image upload started');

            $file = $request->file('profile_image');
            Log::info('File details:', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            try {
                // Delete old image
                if ($user->profile_picture) {
                    $oldImagePath = str_replace('/storage/', '', $user->profile_picture);
                    Storage::disk('public')->delete($oldImagePath);
                    Log::info('Old image deleted: ' . $oldImagePath);
                }

                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                $imagePath = $file->storeAs('profiles', $filename, 'public');
                Log::info('Image stored at: ' . $imagePath);

                if (Storage::disk('public')->exists($imagePath)) {
                    Log::info('File verified to exist in public disk');
                } else {
                    Log::error('File not found after storage: ' . $imagePath);
                }

                $data['profile_picture'] = Storage::url($imagePath);

            } catch (\Exception $e) {
                Log::error('Image upload error: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to upload image: ' . $e->getMessage()
                ], 500);
            }
        }

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
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
