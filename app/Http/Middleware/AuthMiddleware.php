<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Block admin users from API access
            if ($user && $user->is_admin == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Admin users cannot access API endpoints. Please use the admin panel.'
                ], 403);
            }

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized: User not found.'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid or expired token.'
            ], 401);
        }

        return $next($request);
    }
}
