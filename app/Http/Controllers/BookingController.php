<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                return response()->json([
                    'status' => false,
                    'message' => 'You already have a booking for this ride'
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

            // Send notification to driver (you can implement this)
            // $this->sendBookingNotificationToDriver($ride, $booking);

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
            // $this->sendCancellationNotification($booking);

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
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:confirmed,rejected',
                'rejection_reason' => 'required_if:status,rejected|nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $booking = Booking::with('ride.car')->find($bookingId);
            
            if (!$booking) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Check if user is the driver
            if ($booking->ride->car->user_id != Auth::id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Only the driver can update booking status'
                ], 403);
            }

            // Update booking
            $updateData = [
                'status' => $request->status
            ];

            if ($request->status == 'confirmed') {
                $updateData['approved_at'] = Carbon::now();
                $message = 'Booking confirmed successfully';
                
                // Check if there are enough seats
                $availableSeats = $booking->ride->availableSeats();
                if ($booking->seats_booked > $availableSeats) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not enough seats available'
                    ], 400);
                }
            } else {
                $updateData['rejected_at'] = Carbon::now();
                $updateData['rejection_reason'] = $request->rejection_reason;
                $message = 'Booking rejected';
            }

            $booking->update($updateData);

            Log::info('Booking status updated', [
                'booking_id' => $bookingId,
                'new_status' => $request->status,
                'driver_id' => Auth::id()
            ]);

            // Send notification to passenger
            // $this->sendStatusUpdateNotification($booking);

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            Log::error('Update booking status error', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserBookings()
    {
        try {
            $bookings = Booking::with(['ride', 'ride.car', 'ride.driver'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => $bookings,
                'message' => 'Bookings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
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


    private function maskPhoneNumber($phone)
    {
        if (!$phone) return null;
        
        $length = strlen($phone);
        if ($length <= 4) return $phone;
        
        $visible = substr($phone, -4);
        $masked = str_repeat('*', $length - 4) . $visible;
        
        return $masked;
    }
}