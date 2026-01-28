<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Remove this line: echo "xasx";die;
        
        // Add your actual dashboard data here
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $petData = [12, 19, 3, 5, 2, 3, 15, 8, 12, 10, 6, 14]; // Sample data
        $activeUsers = 75; // Sample data
        $inactiveUsers = 25; // Sample data
        
        return view('admin.index', compact('months', 'petData', 'activeUsers', 'inactiveUsers'));
    }
}