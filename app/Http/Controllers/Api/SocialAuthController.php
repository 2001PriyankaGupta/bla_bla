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
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            // Using Socialite to verify the token sent from mobile app
            // Note: Socialite by default handles access_tokens. 
            // For id_tokens, we can use userFromToken if Socialite is configured correctly
            // or verify manually (which you were already doing).
            // However, to strictly use Socialite as requested:
            $googleUser = Socialite::driver('google')->userFromToken($request->id_token);

            if (!$googleUser) {
                return response()->json(['error' => 'Invalid Google Token'], 401);
            }

            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName() ?? 'Google User';
            $picture = $googleUser->getAvatar() ?? null;

            // Find or Create User
            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                // Check if user exists with the same email
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Link Google account
                    $user->google_id = $googleId;
                    if (!$user->profile_picture && $picture) {
                        $user->profile_picture = $picture;
                    }
                    $user->save();
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'google_id' => $googleId,
                        'password' => bcrypt(Str::random(16)), // Use a random password
                        'profile_picture' => $picture,
                        'email_verified_at' => now(),
                        'user_type' => 'passenger', // Default type
                        'status' => 'active'
                    ]);
                }
            }

            // Generate JWT Token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'user' => $user
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Socialite Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
