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
            // First, try verifying as an ID Token (common for mobile apps)
            // Added withoutVerifying() to fix "cURL error 60: SSL certificate problem" on local Windows/WAMP
            $response = Http::withoutVerifying()->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $request->id_token
            ]);

            if ($response->successful()) {
                $googleUser = $response->json();
                $googleId = $googleUser['sub'];
                $email = $googleUser['email'];
                $name = $googleUser['name'] ?? 'Google User';
                $picture = $googleUser['picture'] ?? null;
            } else {
                // Fallback to Socialite userFromToken (expects Access Token)
                try {
                    // Disable SSL verification for Socialite fallback as well for local dev
                    $socialiteUser = Socialite::driver('google')
                        ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                        ->stateless()
                        ->userFromToken($request->id_token);
                    $googleId = $socialiteUser->getId();
                    $email = $socialiteUser->getEmail();
                    $name = $socialiteUser->getName() ?? 'Google User';
                    $picture = $socialiteUser->getAvatar() ?? null;
                } catch (Exception $e) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Invalid Google Token'
                    ], 401);
                }
            }

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
                'status' => 'true',
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => $user
            ]);

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Socialite Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'false',
                'message' => 'Socialite Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
