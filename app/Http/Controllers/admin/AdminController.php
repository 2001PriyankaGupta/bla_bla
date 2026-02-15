<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ride;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Basic Stats
        $totalUsers = User::where('is_admin', 0)->count();
        $totalRides = Ride::count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalRefunds = Payment::where('status', 'refunded')->sum('amount');
        $pendingComplaints = SupportTicket::where('status', 'open')->count();

        // 2. Growth Rates (Last 30 days vs Previous 30 days)
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sixtyDaysAgo = $now->copy()->subDays(60);

        // User Growth
        $currentUsers = User::where('is_admin', 0)->where('created_at', '>=', $thirtyDaysAgo)->count();
        $previousUsers = User::where('is_admin', 0)->where('created_at', '>=', $sixtyDaysAgo)->where('created_at', '<', $thirtyDaysAgo)->count();
        $userGrowth = $previousUsers > 0 ? (($currentUsers - $previousUsers) / $previousUsers) * 100 : 0;

        // Ride Growth
        $currentRides = Ride::where('created_at', '>=', $thirtyDaysAgo)->count();
        $previousRides = Ride::where('created_at', '>=', $sixtyDaysAgo)->where('created_at', '<', $thirtyDaysAgo)->count();
        $rideGrowth = $previousRides > 0 ? (($currentRides - $previousRides) / $previousRides) * 100 : 0;

        // Revenue Growth
        $currentRev = Payment::where('status', 'completed')->where('created_at', '>=', $thirtyDaysAgo)->sum('amount');
        $previousRev = Payment::where('status', 'completed')->where('created_at', '>=', $sixtyDaysAgo)->where('created_at', '<', $thirtyDaysAgo)->sum('amount');
        $revenueGrowth = $previousRev > 0 ? (($currentRev - $previousRev) / $previousRev) * 100 : 0;

        // 3. Chart Data (Last 7 days for Ride Trends)
        $rideTrends = Ride::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $chartLabels = $rideTrends->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('D');
        });
        $chartData = $rideTrends->pluck('count');

        // 4. User Growth Chart (Last 5 months)
        $userGrowthQuery = User::where('is_admin', 0)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%b') as month"),
                DB::raw('count(*) as count'),
                DB::raw('MIN(created_at) as first_of_month')
            )
            ->where('created_at', '>=', $now->copy()->subMonths(5))
            ->groupBy('month')
            ->orderBy('first_of_month')
            ->get();
        
        $userGrowthLabels = $userGrowthQuery->pluck('month');
        $userGrowthData = $userGrowthQuery->pluck('count');

        return view('admin.index', compact(
            'totalUsers', 'totalRides', 'totalRevenue', 'totalRefunds', 'pendingComplaints',
            'userGrowth', 'rideGrowth', 'revenueGrowth',
            'chartLabels', 'chartData',
            'userGrowthLabels', 'userGrowthData'
        ));
    }
}