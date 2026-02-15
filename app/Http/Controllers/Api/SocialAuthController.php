<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $idToken = $request->id_token;

        // Verify the ID Token with Google
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Invalid Google Token'], 401);
        }

        $googleUser = $response->json();

        // Check for required fields
        if (!isset($googleUser['sub']) || !isset($googleUser['email'])) {
            return response()->json(['error' => 'Invalid Google Token Structure'], 401);
        }

        $googleId = $googleUser['sub'];
        $email = $googleUser['email'];
        $name = $googleUser['name'] ?? 'Google User';
        $picture = $googleUser['picture'] ?? null;

        // Find or Create User
        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            // Check if user exists with the same email
            $user = User::where('email', $email)->first();

            if ($user) {
                // Link Google account
                $user->google_id = $googleId;
                if (!$user->profile_picture && $picture) {
                    $user->profile_picture = $picture; // Or handle image download
                }
                $user->save();
            } else {
                // Create new user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => null, // Password is null for social login
                    // 'profile_picture' => $picture, // You might want to download and save this locally or use URL if supported
                    'email_verified_at' => now(),
                    'user_type' => 'passenger', // Default type
                ]);
            }
        }

        // Generate JWT Token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }
}
