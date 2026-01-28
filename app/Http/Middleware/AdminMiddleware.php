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
            return redirect()->route('admin.login')->with('error', 'Please login first');
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
        return $next($request);
    }
}
