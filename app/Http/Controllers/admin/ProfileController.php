<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function profile()
    {
        $id = Auth::id();
        $user = User::find($id);
        return view('admin.settings.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->gender = $request->gender;


        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    public function updateAvatar(Request $request)
    {
        try {
            $user = Auth::user(); // or use JWTAuth if needed
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        Log::info('Avatar upload started');

        $file = $request->file('avatar');
        Log::info('File details:', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        try {
            // Delete old image if exists
            if ($user->profile_picture) {
                $oldImagePath = str_replace('/storage/', '', $user->profile_picture);
                Storage::disk('public')->delete($oldImagePath);
                Log::info('Old image deleted: ' . $oldImagePath);
            }

            // Create new file name & store file in "profiles" folder
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('profiles', $filename, 'public'); // stored in storage/app/public/profiles/

            Log::info('Image stored at: ' . $imagePath);

            if (Storage::disk('public')->exists($imagePath)) {
                Log::info('File verified to exist in public disk');
            } else {
                Log::error('File not found after storage: ' . $imagePath);
            }

            // Update user profile_picture column
            $user->profile_picture = Storage::url($imagePath); // gives /storage/profiles/filename
            $user->save();

        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully!',
            'avatar_url' => $user->profile_picture, // direct URL from storage
        ]);
    }



     public function changePassword(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'current_password'      => 'required',
            'new_password'          => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
}
