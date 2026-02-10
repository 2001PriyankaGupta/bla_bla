<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'ride.car.user'])->latest()->get();
        
        $totalBookings = $bookings->count();
        $confirmedBookings = $bookings->where('status', 'confirmed')->count();
        $pendingBookings = $bookings->where('status', 'pending')->count();
        $rejectedBookings = $bookings->where('status', 'rejected')->count();
        $cancelledBookings = $bookings->where('status', 'cancelled')->count();
        $completedBookings = $bookings->where('status', 'completed')->count();

        return view('admin.bookings.index', compact(
            'bookings',
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'rejectedBookings',
            'cancelledBookings',
            'completedBookings'
        ));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,rejected,cancelled,completed',
        ]);

        $booking->status = $request->status;
        
        if ($request->status == 'rejected') {
            $booking->rejected_at = now();
        } elseif ($request->status == 'confirmed') {
            $booking->approved_at = now();
        }

        $booking->save();

        return response()->json(['success' => true, 'message' => 'Booking status updated successfully']);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
