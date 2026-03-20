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
use App\Mail\UserOtpMail;
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

        // Notify Admin about new user registration
        $admins = User::where('is_admin', 1)->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'title'   => 'New User Registered',
                'message' => "A new user ({$user->email}) has joined the platform.",
                'type'    => 'new_user_registration',
                'data'    => [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'type'    => $user->user_type
                ]
            ]);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addSeconds(60);
        $user->save();

        try {
            Mail::to($user->email)->send(new UserOtpMail($otp));
        } catch (\Exception $e) {
            Log::error("OTP Mail Error: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'pending_verification',
            'message' => 'User registered. OTP sent to your email. Please verify.',
            'email' => $user->email
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

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addSeconds(60);
        $user->save();

        try {
            Mail::to($user->email)->send(new UserOtpMail($otp));
        } catch (\Exception $e) {
            Log::error("OTP Mail Error: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'pending_verification',
            'message' => 'Login attempt successful. OTP sent to your email. Please verify.',
            'email' => $user->email
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'error' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->otp !== $request->otp) {
            return response()->json([
                'status' => 'false',
                'message' => 'Invalid OTP code. Please check and try again.'
            ], 400);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => 'false',
                'message' => 'Your OTP has expired, please resend OTP.'
            ], 400);
        }

        // Clear OTP
        $user->otp = null;
        $user->otp_expires_at = null;
        if (!$user->email_verified_at) {
            $user->email_verified_at = Carbon::now();
        }
        $user->save();

        // Generate Token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'true',
            'message' => 'OTP verified successfully.',
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

    public function resendOtp(Request $request)
    {
        Log::info("Resend OTP request for email: " . $request->email);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            Log::warning("Resend OTP Validation failed: " . $validator->errors()->first());
            return response()->json([
                'status' => 'false',
                'error' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Generate new OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addSeconds(60);
        $user->save();

        Log::info("Generated new OTP für {$user->email}: {$otp}");

        try {
            Mail::to($user->email)->send(new UserOtpMail($otp));
            Log::info("OTP Mail sent successfully to " . $user->email);
            return response()->json([
                'status' => 'true',
                'message' => 'A new OTP code has been sent to your email.'
            ]);
        } catch (\Exception $e) {
            Log::error("Resend OTP Mail Error: " . $e->getMessage());
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }
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
