<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    if (Auth::check() && Auth::user()->is_admin == 1) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});