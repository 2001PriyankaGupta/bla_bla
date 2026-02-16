<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialController extends Controller
{
    /**
     * Handle Google Login from Mobile App
     * Expects a 'token' (either access_token or id_token)
     */
    public function handleGoogleLogin(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        try {
            // Socialite can handle user retrieval from token
            // Note: For mobile, it's often the access_token or id_token
            $googleUser = Socialite::driver('google')->userFromToken($request->token);

            if (!$googleUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Google Token'
                ], 401);
            }

            // Find or Create user
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user if not exists
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)), // Random password
                    'profile_picture' => $googleUser->getAvatar(),
                    'user_type' => 'passenger', // Default type
                    'status' => 'active'
                ]);
            } else {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }

            // Generate JWT Token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Google Authentication Failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
