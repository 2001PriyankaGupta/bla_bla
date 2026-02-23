<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

use Exception;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function googleRegister(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $request->id_token
                ]);

            if ($response->successful()) {
                $googleUser = $response->json();
                $googleId = $googleUser['sub'];
                $email = $googleUser['email'];
                $name = $googleUser['name'] ?? 'Google User';
                $picture = $googleUser['picture'] ?? null;
            } else {
                return response()->json(['status' => 'false', 'message' => 'Invalid Google Token'], 401);
            }

            // Check if user already exists
            $user = User::where('google_id', $googleId)->orWhere('email', $email)->first();

            if ($user) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'This email already exists'
                ], 400);
            }

            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'password' => bcrypt(Str::random(16)),
                'profile_picture' => $picture,
                'email_verified_at' => now(),
                'user_type' => 'passenger',
                'status' => 'active'
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'true',
                'message' => 'Registration successful',
                'access_token' => $token,
                'user' => $user
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
        }
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $request->id_token
                ]);

            if ($response->successful()) {
                $googleUser = $response->json();
                $googleId = $googleUser['sub'];
                $email = $googleUser['email'];
            } else {
                return response()->json(['status' => 'false', 'message' => 'Invalid Google Token'], 401);
            }

            // Find User
            $user = User::where('google_id', $googleId)->orWhere('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'This email ID does not exist'
                ], 404);
            }

            // Update google_id if it was missing but email matched
            if (!$user->google_id) {
                $user->google_id = $googleId;
                $user->save();
            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'true',
                'message' => 'Login successful',
                'access_token' => $token,
                'user' => $user
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
        }
    }
}
