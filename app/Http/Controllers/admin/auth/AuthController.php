<?php

namespace App\Http\Controllers\admin\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check() && Auth::user()->is_admin == 1) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        Log::info('Login attempt', ['email' => $request->email]);

        if (Auth::attempt($request->only('email', 'password'))) {
            Log::info('Login successful', [
                'user_id' => Auth::id(),
                'is_admin' => Auth::user()->is_admin,
                'user' => Auth::user()
            ]);

            if (Auth::user()->is_admin == 1) {
                Log::info('User is admin, redirecting to dashboard');
                return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
            } else {
                Log::warning('User is not admin', ['user_id' => Auth::id()]);
                Auth::logout();
                return redirect()->route('admin.login')->with('error', 'Unauthorized access');
            }
        }
        Log::warning('Login failed', ['email' => $request->email]);
        return redirect()->route('admin.login')->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        Log::info('Admin logout', ['user_id' => Auth::id()]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}
