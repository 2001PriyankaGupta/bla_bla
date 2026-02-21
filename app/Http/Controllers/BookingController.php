<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ride;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Carbon\Carbon;

class BookingController extends Controller
{
    
    public function bookRide(Request $request, $rideId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'seats' => 'required|integer|min:1',
                'special_requests' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            // Find the ride
            $ride = Ride::with('car')->find($rideId);
            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            // Check if ride is in the future
            if (Carbon::parse($ride->date_time)->isPast()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot book a past ride'
                ], 400);
            }

            // Check available seats
            $availableSeats = $ride->availableSeats();
            if ($request->seats > $availableSeats) {
                return response()->json([
                    'status' => false,
                    'message' => "Only {$availableSeats} seats available"
                ], 400);
            }

            // Check if user already has a pending/confirmed booking for this ride
            $existingBooking = Booking::where('ride_id', $rideId)
                ->where('user_id', Auth::id())
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if ($existingBooking) {
                // If the booking is pending, return it so the user can pay (Status true, but maybe a message indicating it exists)
                if ($existingBooking->status === 'pending') {
                    // Update seats if needed or just return current
                     // Calculate total price
                    $totalPrice = $ride->price_per_seat * $existingBooking->seats_booked;

                    return response()->json([
                        'status' => true,
                        'message' => 'Pending booking found. Proceeding to payment.',
                        'data' => [
                            'booking_id' => $existingBooking->id,
                            'ride_details' => [
                                'pickup' => $ride->pickup_point,
                                'drop' => $ride->drop_point,
                                'date_time' => $ride->date_time,
                                'seats_booked' => $existingBooking->seats_booked,
                                'total_price' => $totalPrice,
                                'status' => 'pending'
                            ],
                            'driver_contact' => [
                                'name' => $ride->car->user->name ?? 'Driver',
                                'phone' => $ride->car->user->phone ?? null
                            ]
                        ]
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'You already have a confirmed booking for this ride'
                ], 400);
            }

            // Calculate total price
            $totalPrice = $ride->price_per_seat * $request->seats;

            // Create booking
            $booking = Booking::create([
                'ride_id' => $rideId,
                'user_id' => Auth::id(),
                'seats_booked' => $request->seats,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'special_requests' => $request->special_requests
            ]);

            Log::info('Booking created', [
                'booking_id' => $booking->id,
                'ride_id' => $rideId,
                'user_id' => Auth::id(),
                'seats' => $request->seats
            ]);

            // Send notification to driver
            $driverId = $ride->car->user_id;
            Notification::create([
                'user_id' => $driverId,
                'title' => 'New Booking Request',
                'message' => Auth::user()->name . ' has requested ' . $request->seats . ' seat(s) for your ride from ' . $ride->pickup_point . ' to ' . $ride->drop_point,
                'type' => 'booking_request',
                'data' => [
                    'ride_id' => $ride->id,
                    'booking_id' => $booking->id,
                    'passenger_name' => Auth::user()->name
                ]
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Ride booked successfully. Waiting for driver approval.',
                'data' => [
                    'booking_id' => $booking->id,
                    'ride_details' => [
                        'pickup' => $ride->pickup_point,
                        'drop' => $ride->drop_point,
                        'date_time' => $ride->date_time,
                        'seats_booked' => $request->seats,
                        'total_price' => $totalPrice,
                        'status' => 'pending'
                    ],
                    'driver_contact' => [
                        'name' => $ride->car->user->name ?? 'Driver',
                        'phone' => $ride->car->user->phone ?? null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Booking error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

   
    public function cancelBooking($bookingId)
    {
        try {
            $booking = Booking::with('ride')->find($bookingId);
            
            if (!$booking) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Check if user owns the booking
            if ($booking->user_id != Auth::id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized to cancel this booking'
                ], 403);
            }

            // Check if booking can be cancelled
            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot cancel a ' . $booking->status . ' booking'
                ], 400);
            }

            // Update booking status
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now()
            ]);

            // Update user's cancelled rides count
            $user = Auth::user();
            $user->increment('cancelled_rides');

            Log::info('Booking cancelled', [
                'booking_id' => $bookingId,
                'user_id' => Auth::id(),
                'ride_id' => $booking->ride_id
            ]);

            // Send notification to driver
            $driverId = $booking->ride->car->user_id;
            Notification::create([
                'user_id' => $driverId,
                'title' => 'Booking Cancelled',
                'message' => Auth::user()->name . ' has cancelled their booking for your ride from ' . $booking->ride->pickup_point . ' to ' . $booking->ride->drop_point,
                'type' => 'booking_cancelled_by_passenger',
                'data' => [
                    'ride_id' => $booking->ride_id,
                    'booking_id' => $booking->id,
                    'passenger_name' => Auth::user()->name
                ]
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Booking cancelled successfully',
                'data' => [
                    'refund_info' => $this->calculateRefund($booking),
                    'cancellation_time' => Carbon::now()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Cancel booking error', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function updateBookingStatus(Request $request, $bookingId)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update booking status.'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:confirmed,rejected,completed',
                'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $booking = Booking::with(['ride.car', 'user'])->find($bookingId);
            
            if (!$booking) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Check if this user owns the car for this ride
            if ($booking->ride->car->user_id != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You can only update bookings for your own rides.'
                ], 403);
            }

            // Check if booking is in a manageable state
            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot update status for a ' . $booking->status . ' booking.'
                ], 400);
            }

            // Status update validation
            if ($booking->status === 'confirmed' && $request->status === 'rejected') {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot reject an already confirmed booking.'
                ], 400);
            }

            if ($request->status === 'completed' && $booking->status !== 'confirmed') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only confirmed bookings can be marked as completed.'
                ], 400);
            }

            // Update booking
            $updateData = [
                'status' => $request->status
            ];

            if ($request->status == 'confirmed') {
                $updateData['approved_at'] = Carbon::now();
                
                // Check if there are enough seats
                $availableSeats = $booking->ride->availableSeats();
                if ($booking->seats_booked > $availableSeats) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not enough seats available. Only ' . $availableSeats . ' seat(s) left.'
                    ], 400);
                }
                
                $message = 'Booking confirmed successfully';
            } elseif ($request->status == 'completed') {
                $updateData['completed_at'] = Carbon::now();
                $message = 'Ride marked as completed';
            } else {
                $updateData['rejected_at'] = Carbon::now();
                $updateData['rejection_reason'] = $request->rejection_reason;
                $message = 'Booking rejected successfully';
            }

            $booking->update($updateData);

            // Refresh booking with relationships
            $booking->refresh();

            Log::info('Booking status updated', [
                'booking_id' => $bookingId,
                'new_status' => $request->status,
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);

            // Send notification to passenger
            $statusTitle = $request->status == 'confirmed' ? 'Booking Confirmed' : 
                          ($request->status == 'completed' ? 'Ride Completed' : 'Booking Rejected');
            
            $statusMessage = $request->status == 'confirmed' 
                ? 'Your ride from ' . $booking->ride->pickup_point . ' has been confirmed by ' . $user->name 
                : ($request->status == 'completed' 
                    ? 'Your ride from ' . $booking->ride->pickup_point . ' has been marked as completed. Hope you had a great trip!'
                    : 'Your ride request from ' . $booking->ride->pickup_point . ' has been rejected by ' . $user->name . '. Reason: ' . ($request->rejection_reason ?? 'No reason provided'));

            Notification::create([
                'user_id' => $booking->user_id,
                'title' => $statusTitle,
                'message' => $statusMessage,
                'type' => 'booking_status_updated',
                'data' => [
                    'ride_id' => $booking->ride_id,
                    'booking_id' => $booking->id,
                    'new_status' => $request->status
                ]
            ]);

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            Log::error('Update booking status error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserBookings(Request $request)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to view bookings.'
                ], 401);
            }

            // Fetch passenger bookings
            $passengerBookings = Booking::with(['ride', 'ride.car', 'ride.driver'])
                ->where('user_id', $user->id)
                ->get()
                ->map(function($booking) {
                    return [
                        'id' => $booking->id,
                        'user_type' => 'passenger',
                        'time' => $booking->ride ? $booking->ride->start_time_formatted : 'N/A',
                        'location' => $booking->ride ? $booking->ride->pickup_point : 'N/A',
                        'destination' => $booking->ride ? $booking->ride->drop_point : 'N/A',
                        'date_display' => $booking->ride && $booking->ride->date_time ? Carbon::parse($booking->ride->date_time)->format('M d, Y') : 'N/A',
                        'price' => $booking->total_price ? '₹' . number_format($booking->total_price, 2) : '₹0.00',
                        'status' => $booking->status,
                        'seats_booked' => $booking->seats_booked,
                        'booking_date' => $booking->created_at->format('M d, Y h:i A'),
                        'driver_details' => $booking->ride && $booking->ride->car && $booking->ride->car->user ? [
                            'driver_name' => $booking->ride->car->user->name,
                            'driver_phone' => $booking->ride->car->user->phone,
                            // 'driver_rating' => $booking->ride->car->user->rating
                        ] : null,
                        'car_details' => $booking->ride && $booking->ride->car ? [
                            'car_model' => $booking->ride->car->car_model,
                            'car_number' => $booking->ride->car->licence_plate ,
                            'car_type' => $booking->ride->car->car_make,
                            'car_photo' => $booking->ride->car->car_photo ? $booking->ride->car->car_photo : null
                        ] : null
                    ];
                });

            // Fetch driver bookings (bookings on rides published by this user)
            $driverBookings = Booking::with(['ride', 'ride.car', 'user'])
                ->whereHas('ride.car', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->get()
                ->map(function($booking) {
                    return [
                        'id' => $booking->id,
                        'user_type' => 'driver',
                        'passenger_name' => $booking->user ? $booking->user->name : 'N/A',
                        'passenger_email' => $booking->user ? $booking->user->email : 'N/A',
                        'passenger_phone' => $booking->user ? $booking->user->phone : 'N/A',
                        'time' => $booking->ride ? $booking->ride->start_time_formatted : 'N/A',
                        'location' => $booking->ride ? $booking->ride->pickup_point : 'N/A',
                        'destination' => $booking->ride ? $booking->ride->drop_point : 'N/A',
                        'date_display' => $booking->ride && $booking->ride->date_time ? Carbon::parse($booking->ride->date_time)->format('M d, Y') : 'N/A',
                        'price' => $booking->total_price ? '₹' . number_format($booking->total_price, 2) : '₹0.00',
                        'status' => $booking->status,
                        'seats_booked' => $booking->seats_booked,
                        'booking_date' => $booking->created_at->format('M d, Y h:i A'),
                        'can_manage' => in_array($booking->status, ['pending', 'confirmed']),
                        'ride_details' => $booking->ride ? [
                            'ride_id' => $booking->ride->id,
                            'car_model' => $booking->ride->car ? $booking->ride->car->car_model : 'N/A',
                            'car_number' => $booking->ride->car ? $booking->ride->car->licence_plate : 'N/A'
                        ] : null,
                        'car_details' => $booking->ride && $booking->ride->car ? [
                            'car_model' => $booking->ride->car->car_model,
                            'car_number' => $booking->ride->car->licence_plate ,
                            'car_type' => $booking->ride->car->car_make,
                            'car_photo' => $booking->ride->car->car_photo ? $booking->ride->car->car_photo : null
                        ] : null
                    ];
                });

            // Merge and sort by date
            $allBookings = $passengerBookings->concat($driverBookings)->sortByDesc('booking_date')->values();

            return response()->json([
                'status' => true,
                'data' => [
                    'user_type' => 'mixed',
                    'bookings' => $allBookings,
                ],
                'message' => 'Bookings retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }


    // Helper function for date display like "Today", "Tomorrow", or date
    private function getDateDisplay($rideDate)
    {
        if (!$rideDate) return '';
        
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        $rideDate = Carbon::parse($rideDate)->startOfDay();
        
        if ($rideDate->equalTo($today)) {
            return 'Today';
        } elseif ($rideDate->equalTo($tomorrow)) {
            return 'Tomorrow';
        } else {
            return $rideDate->format('M d, Y');
        }
    }

    
    private function calculateRefund($booking)
    {
        $rideDateTime = Carbon::parse($booking->ride->date_time);
        $hoursBeforeRide = $rideDateTime->diffInHours(Carbon::now(), false);
        
        if ($hoursBeforeRide >= 24) {
            // Full refund if cancelled 24+ hours before
            return [
                'refund_amount' => $booking->total_price,
                'refund_percentage' => 100,
                'refund_time' => '24+ hours before ride'
            ];
        } elseif ($hoursBeforeRide >= 12) {
            // 50% refund if cancelled 12-24 hours before
            return [
                'refund_amount' => $booking->total_price * 0.5,
                'refund_percentage' => 50,
                'refund_time' => '12-24 hours before ride'
            ];
        } else {
            // No refund if cancelled less than 12 hours before
            return [
                'refund_amount' => 0,
                'refund_percentage' => 0,
                'refund_time' => 'Less than 12 hours before ride'
            ];
        }
    }


 
    public function getBookingConfirmation($bookingId)
    {
        $user = Auth::user();
        
        $booking = Booking::with(['ride.driver', 'ride.car'])
            ->where('id', $bookingId)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('ride', function($q) use ($user) {
                        $q->where('driver_id', $user->id);
                    });
            })
            ->first();
            
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized'
            ], 404);
        }
        
        // Calculate ETA (simplified - you can integrate Google Maps API here)
        $eta = rand(3, 10); // Random ETA between 3-10 minutes for demo
        
        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'booking_code' => 'BK' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'status' => $booking->status,
                'pickup_location' => $booking->pickup_location,
                'dropoff_location' => $booking->dropoff_location,
                'booked_seats' => $booking->booked_seats,
                'total_price' => $booking->total_price,
                'payment_status' => $booking->payment_status ?? 'pending',
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                'driver' => [
                    'id' => $booking->ride->driver->id,
                    'name' => $booking->ride->driver->name,
                    'phone' => $this->maskPhoneNumber($booking->ride->driver->phone),
                    'profile_picture' => $booking->ride->driver->profile_picture,
                    'rating' => $booking->ride->driver->rating ?? 0,
                    'total_rides' => $booking->ride->driver->rides()->count(),
                    'eta' => $eta,
                    'eta_text' => 'Arriving in ' . $eta . ' min'
                ],
                'car' => [
                    'model' => $booking->ride->car->model,
                    'brand' => $booking->ride->car->brand ?? 'Unknown',
                    'color' => $booking->ride->car->color,
                    'license_plate' => $booking->ride->car->license_plate,
                    'car_image' => $booking->ride->car->car_image ?? null
                ],
                'ride' => [
                    'departure_time' => $booking->ride->departure_time,
                    'departure_date' => $booking->ride->departure_date,
                    'estimated_arrival' => $booking->ride->estimated_arrival,
                    'pickup_point' => $booking->ride->pickup_point,
                    'dropoff_point' => $booking->ride->dropoff_point
                ]
            ],
            'actions' => [
                'can_call' => true,
                'can_message' => true,
                'can_cancel' => $booking->status == 'confirmed' || $booking->status == 'pending',
                'can_share' => true
            ]
        ]);
    }


    public function reduceSeats(Request $request, $bookingId)
    {
        Log::info('reduceSeats called', [
            'booking_id' => $bookingId,
            'new_seats' => $request->new_seats,
            'user_id' => Auth::id()
        ]);

        try {
            $user = Auth::user();
            $validator = Validator::make($request->all(), [
                'new_seats' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'error' => $validator->errors()->first()], 422);
            }

            $booking = Booking::with('ride.car.user')->find($bookingId);
            if (!$booking) {
                return response()->json(['status' => false, 'message' => 'Booking not found'], 404);
            }

            if ($booking->user_id != $user->id) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return response()->json(['status' => false, 'message' => 'Cannot modify ' . $booking->status . ' booking'], 400);
            }

            if ($request->new_seats >= $booking->seats_booked) {
                return response()->json(['status' => false, 'message' => 'New seats must be less than current booked seats'], 400);
            }

            $oldSeats = $booking->seats_booked;
            $newPrice = $booking->ride->price_per_seat * $request->new_seats;

            $booking->update([
                'seats_booked' => $request->new_seats,
                'total_price' => $newPrice
            ]);

            // Notify driver
            Notification::create([
                'user_id' => $booking->ride->car->user_id,
                'title' => 'Seats Reduced',
                'message' => $user->name . " reduced their seats from {$oldSeats} to {$request->new_seats} for your ride.",
                'type' => 'booking_updated',
                'data' => [
                    'ride_id' => $booking->ride_id,
                    'booking_id' => $booking->id,
                    'old_seats' => $oldSeats,
                    'new_seats' => $request->new_seats
                ]
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Seats reduced successfully',
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            Log::error('ReduceSeats Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    private function maskPhoneNumber($phone)
// ... existing maskPhoneNumber code ...
    {
        if (!$phone) return null;
        
        $length = strlen($phone);
        if ($length <= 4) return $phone;
        
        $visible = substr($phone, -4);
        $masked = str_repeat('*', $length - 4) . $visible;
        
        return $masked;
    }
}