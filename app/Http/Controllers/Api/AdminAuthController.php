<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use App\Models\PasswordResetCode;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminAuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone' => 'required|string|min:10|max:15|unique:users,phone',
                'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->first()[0];
            return response()->json([
                'status' => 'false',
                'error' => $firstError
            ], 422);
        }

        $profilePicturePath = null;

        if ($request->hasFile('profile_image')) {
            try {
                $file = $request->file('profile_image');
                $filename = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('profiles', $filename, 'public');
                $profilePicturePath = Storage::url($imagePath);
            } catch (\Exception $e) {
                Log::error('Profile image upload failed: ' . $e->getMessage());
                return response()->json([
                    'status' => 'false',
                    'error' => 'Failed to upload profile image.'
                ], 500);
            }
        }

        $user = User::create([
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
            'user_type' => 'passenger',
            'profile_picture' => $profilePicturePath,
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'true',
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'error' => $validator->errors()->first()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'false',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth('api')->user();

        // Ensure only non-admin users can login
        if ($user->is_admin == 1) {
            auth('api')->logout();
            return response()->json([
                'status' => 'false',
                'message' => 'This account must login through the admin panel.'
            ], 403);
        }

        return response()->json([
            'status' => 'true',
            'message' => 'Login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function logout(Request $request)
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'true',
            'message' => 'Logged out successfully'
        ]);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function forgotPassword(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'message' => $validator->errors()->first()], 422);
        }

        $email = $request->email;
        $code = Str::random(6); // Or numeric: rand(100000, 999999)
        // Store code
        PasswordResetCode::updateOrCreate(
            ['email' => $email],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        // Send Email
        try {
            Mail::to($email)->send(new ForgotPasswordMail($code));
        } catch (\Exception $e) {
             Log::error("Mail Error: " . $e->getMessage());
             return response()->json(['status' => 'false', 'message' => 'Failed to send email. but code generated for testing: ' . $code], 500);
        }

        return response()->json(['status' => 'true', 'message' => 'Reset code sent to your email.', 'code_debug' => $code]); // Remove code_debug in prod
    }

    public function verifyResetCode(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code'  => 'required'
        ]);

        if ($validator->fails()) {
             return response()->json(['status' => 'false', 'message' => $validator->errors()->first()], 422);
        }

        $record = PasswordResetCode::where('email', $request->email)->where('code', $request->code)->first();

        if (!$record || Carbon::now()->greaterThan($record->expires_at)) {
             return response()->json(['status' => 'false', 'message' => 'Invalid or expired code.'], 400);
        }

        return response()->json(['status' => 'true', 'message' => 'Code verified.']);
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required',
            'password' => 'required|min:6|confirmed' // expect password_confirmation field
        ]);

        if ($validator->fails()) {
              return response()->json(['status' => 'false', 'message' => $validator->errors()->first()], 422);
        }

        // Verify again just in case
        $record = PasswordResetCode::where('email', $request->email)->where('code', $request->code)->first();

        if (!$record || Carbon::now()->greaterThan($record->expires_at)) {
             return response()->json(['status' => 'false', 'message' => 'Invalid or expired code.'], 400);
        }

        // Update User
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete code
        $record->delete();

        return response()->json(['status' => 'true', 'message' => 'Password reset successfully.']);
    }
}
