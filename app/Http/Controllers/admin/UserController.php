<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $query = User::where('id', '!=', auth()->id());
        
        // Base counts for statistics cards (always accurate)
        $stats = [
            'total' => User::where('id', '!=', auth()->id())->count(),
            'active' => User::where('id', '!=', auth()->id())->where('status', 'active')->count(),
            'inactive' => User::where('id', '!=', auth()->id())->where('status', 'inactive')->count(),
            'drivers' => User::where('id', '!=', auth()->id())->where('user_type', 'driver')->count(),
            'passengers' => User::where('id', '!=', auth()->id())->where('user_type', 'passenger')->count(),
        ];

        // Apply filters for the table data
        if ($request->has('type') && in_array($request->type, ['passenger', 'driver'])) {
            $query->where('user_type', $request->type);
        }
        
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }
        
        $users = $query->withCount(['bookings', 'rides'])
            ->withAvg('driverReviews', 'rating')
            ->withAvg('passengerReviews', 'rating')
            ->orderBy('created_at', 'desc')
            ->get(); // Using get() for client-side DataTables

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users,phone',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:passenger,driver',
        ], [
            'name.regex' => 'The name field should only contain alphabets and spaces.',
            'phone.regex' => 'The phone number must be exactly 10 digits!'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'is_admin' => 0, // Regular user
            'user_type' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::withCount(['bookings', 'rides'])
            ->withAvg('driverReviews', 'rating')
            ->withAvg('passengerReviews', 'rating')
            ->findOrFail($id);
            
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|regex:/^[0-9]{10}$/|unique:users,phone,' . $user->id,
            'gender' => 'nullable|in:male,female,other',
            'locality' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'The name field should only contain alphabets and spaces.',
            'phone.regex' => 'The phone number must be exactly 10 digits!',
            'phone.unique' => 'This phone number is already registered with another user.'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'locality' => $request->locality,

        ];
        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                unlink(public_path($user->profile_picture));
            }

            $image = $request->file('profile_picture');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'uploads/profiles/' . $imageName;
            $image->move(public_path('uploads/profiles'), $imageName);
            
            $updateData['profile_picture'] = $imagePath;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $userId = $user->id;

            // 1. If User has cars, delete associated items
            $cars = \App\Models\Car::where('user_id', $userId)->get();
            foreach ($cars as $car) {
                $rides = \App\Models\Ride::where('car_id', $car->id)->get();
                foreach ($rides as $ride) {
                    \App\Models\Message::where('ride_id', $ride->id)->delete();
                    \App\Models\StopPoint::where('ride_id', $ride->id)->delete();
                    
                    $bookings = \App\Models\Booking::where('ride_id', $ride->id)->get();
                    foreach ($bookings as $booking) {
                        \App\Models\Payment::where('booking_id', $booking->id)->delete();
                        $booking->delete();
                    }
                    $ride->delete();
                }
                $car->delete();
            }

            // 2. Delete bookings made by the user (as passenger)
            $userBookings = \App\Models\Booking::where('user_id', $userId)->get();
            foreach ($userBookings as $booking) {
                \App\Models\Payment::where('booking_id', $booking->id)->delete();
                $booking->delete();
            }

            // 3. Delete reviews related to the user
            \App\Models\Review::where('driver_id', $userId)
                ->orWhere('user_id', $userId)
                ->orWhere('reviewed_by', $userId)
                ->delete();

            // 4. Delete Support Tickets & Replies
            $tickets = \App\Models\SupportTicket::where('user_id', $userId)->get();
            foreach ($tickets as $ticket) {
                \App\Models\TicketReply::where('ticket_id', $ticket->id)->delete();
                $ticket->delete();
            }
            \App\Models\TicketReply::where('user_id', $userId)->delete();

            // 5. Delete Messages where user is sender or receiver
            if (class_exists(\App\Models\Message::class)) {
                \App\Models\Message::where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId)
                    ->delete();
            }

            // 6. Delete Notifications
            \App\Models\Notification::where('user_id', $userId)->delete();

            // Finally, delete the User
            $user->delete();

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User and all associated details deleted successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('admin.users.index')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'User status updated successfully.');
    }
}