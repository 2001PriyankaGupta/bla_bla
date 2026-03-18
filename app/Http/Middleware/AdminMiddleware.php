<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
   
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect()->route('admin.login')->with('error', 'Session Expired! Please login again.');
        }

        if (Auth::user()->is_admin != 1) {
            Log::warning('User is not admin', [
                'user_id' => Auth::id(),
                'is_admin' => Auth::user()->is_admin
            ]);
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        Log::info('AdminMiddleware passed, user is admin');
        
        $response = $next($request);

        // Prevent browser caching
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
