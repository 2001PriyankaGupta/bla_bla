<?php
// app/Http/Controllers/Api/RideController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\Car;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RideController extends Controller
{
    public function index(Request $request)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to view rides.'
                ], 401);
            }

            $rides = Ride::with(['car', 'car.user'])
                        ->whereHas('car', function($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->latest()
                        ->get();

            return response()->json([
                'status' => true,
                'message' => 'Rides fetched successfully',
                'data' => $rides
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to create a ride.'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'pickup_point' => 'required|string|max:255',
                'drop_point' => 'required|string|max:255',
                'date_time' => 'required|date',
                'total_seats' => 'required|integer|min:1',
                'price_per_seat' => 'required|numeric|min:0',
                'car_make' => 'required|string|exists:cars,car_make,user_id,' . $user->id, // Fix typo
                'luggage_allowed' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            // Find car by car_make and user_id
            $car = Car::where('car_make', $request->car_make)
                    ->where('user_id', $user->id)
                    ->first();

            if (!$car) {
                return response()->json([
                    'status' => false,
                    'error' => 'Car not found or you do not have permission to use this car.'
                ], 404);
            }

            $ride = Ride::create([
                'pickup_point' => $request->pickup_point,
                'drop_point' => $request->drop_point,
                'date_time' => $request->date_time,
                'total_seats' => $request->total_seats,
                'price_per_seat' => $request->price_per_seat,
                'car_id' => $car->id,
                'luggage_allowed' => $request->luggage_allowed,
            ]);

            $ride->load('car');

            return response()->json([
                'status' => true,
                'message' => 'Ride created successfully'

            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to view ride details.'
                ], 401);
            }

            $ride = Ride::with(['car', 'car.user'])
                       ->whereHas('car', function($query) use ($user) {
                           $query->where('user_id', $user->id);
                       })
                       ->find($id);

            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Ride fetched successfully',
                'data' => $ride
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update ride.'
                ], 401);
            }

            $ride = Ride::whereHas('car', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->find($id);

            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'pickup_point' => 'sometimes|string|max:255', // sometimes instead of required
                'drop_point' => 'sometimes|string|max:255',
                'date_time' => 'sometimes|date',
                'total_seats' => 'sometimes|integer|min:1',
                'price_per_seat' => 'sometimes|numeric|min:0',
                'car_make' => 'sometimes|string|exists:cars,car_make,user_id,' . $user->id, // sometimes for update
                'luggage_allowed' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            // Update car if car_make is provided
            if ($request->has('car_make')) {
                $car = Car::where('car_make', $request->car_make) // car_make instead of name
                        ->where('user_id', $user->id)
                        ->first();

                if (!$car) {
                    return response()->json([
                        'status' => false,
                        'error' => 'Car not found or you do not have permission to use this car.'
                    ], 404);
                }
                $ride->car_id = $car->id;
            }

            // Update other fields
            $ride->update([
                'pickup_point' => $request->pickup_point ?? $ride->pickup_point,
                'drop_point' => $request->drop_point ?? $ride->drop_point,
                'date_time' => $request->date_time ?? $ride->date_time,
                'total_seats' => $request->total_seats ?? $ride->total_seats,
                'price_per_seat' => $request->price_per_seat ?? $ride->price_per_seat,
                'luggage_allowed' => $request->luggage_allowed ?? $ride->luggage_allowed,
            ]);

            $ride->load('car');

            return response()->json([
                'status' => true,
                'message' => 'Ride updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to delete ride.'
                ], 401);
            }

            $ride = Ride::whereHas('car', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->find($id);

            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            $ride->delete();

            return response()->json([
                'status' => true,
                'message' => 'Ride deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function shareRide(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'share_with' => 'required|array',
            'share_with.*' => 'email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        $ride = Ride::with('car')->find($id);
        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Ride not found'
            ], 404);
        }

        // Generate shareable link
        $shareLink = url("/ride/{$id}");
        
        // Here you can implement email sending logic
        // foreach ($request->share_with as $email) {
        //     Mail::to($email)->send(new RideShareMail($ride, $shareLink));
        // }

        return response()->json([
            'status' => true,
            'message' => 'Ride shared successfully',
            'data' => [
                'share_link' => $shareLink,
                'shared_with' => $request->share_with,
                'ride_details' => [
                    'pickup' => $ride->pickup_point,
                    'drop' => $ride->drop_point,
                    'date_time' => $ride->date_time,
                    'price' => $ride->price_per_seat
                ]
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}


    public function contactDriver(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            // Get authenticated user
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Find ride with driver information using raw query
            $rideData = \DB::table('rides')
                ->join('cars', 'rides.car_id', '=', 'cars.id')
                ->leftJoin('users as drivers', 'cars.user_id', '=', 'drivers.id')
                ->where('rides.id', $id)
                ->select(
                    'rides.*',
                    'cars.id as car_id',
                    'drivers.id as driver_id',
                    'drivers.name as driver_name',
                    'drivers.phone as driver_phone',
                    'drivers.email as driver_email',
                    'drivers.profile_picture as driver_profile_picture'
                )
                ->first();

            if (!$rideData) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            // Check if driver exists
            if (!$rideData->driver_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Driver information not available for this ride'
                ], 404);
            }

            // Save message to database
            $message = \App\Models\Message::create([
                'ride_id' => $rideData->id,
                'sender_id' => $user->id,
                'receiver_id' => $rideData->driver_id,
                'message' => $request->message,
                'is_read' => false
            ]);

            // Prepare response
            $driverInfo = [
                'name' => $rideData->driver_name ?? 'Driver',
                'phone' => $rideData->driver_phone ?? 'Not available',
                'email' => $rideData->driver_email ?? 'Not available',
                'profile_picture' => $rideData->driver_profile_picture ?? null,
                'driver_id' => $rideData->driver_id
            ];

            return response()->json([
                'status' => true,
                'message' => 'Message sent to driver successfully',
                'data' => [
                    'driver' => $driverInfo,
                    'sender' => [
                        'id' => $user->id,
                        'name' => $user->name
                    ],
                    'ride_info' => [
                        'ride_id' => $rideData->id,
                        'pickup' => $rideData->pickup_point,
                        'drop' => $rideData->drop_point,
                        'date_time' => $rideData->date_time
                    ],
                    'message_details' => [
                        'id' => $message->id,
                        'content' => $request->message,
                        'sent_at' => $message->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Contact driver error: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Unable to send message. Please try again.'
            ], 500);
        }
    }

    public function searchRides(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'from' => 'required|string|max:255',
                'to' => 'required|string|max:255',
                'departing' => 'required|date',
                'passengers' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $from = trim($request->from);
            $to = trim($request->to);
            $departing = $request->departing;
            $passengers = $request->passengers;

            // IMPORTANT FIX: सिर्फ date compare करें, time नहीं
            $departingDate = Carbon::parse($departing)->toDateString(); // "2025-12-01"

            Log::info('=== SEARCH PARAMETERS ===');
            Log::info('From: ' . $from);
            Log::info('To: ' . $to);
            Log::info('Date: ' . $departingDate);
            Log::info('Passengers: ' . $passengers);

            // Check database directly
            $allRides = Ride::all();
            Log::info('=== ALL RIDES IN DATABASE ===');
            foreach ($allRides as $ride) {
                $rideDate = Carbon::parse($ride->date_time)->toDateString();
                Log::info('Ride ID: ' . $ride->id . 
                        ' | From: ' . $ride->pickup_point . 
                        ' | To: ' . $ride->drop_point . 
                        ' | Date in DB: ' . $ride->date_time .
                        ' | Formatted Date: ' . $rideDate);
            }

            // FIXED QUERY: Use DATE() function for comparison
            $rides = Ride::with(['car', 'car.user'])
                        ->where(function($query) use ($from) {
                            $fromLower = strtolower($from);
                            $query->whereRaw('LOWER(pickup_point) LIKE ?', ['%' . $fromLower . '%']);
                        })
                        ->where(function($query) use ($to) {
                            $toLower = strtolower($to);
                            $query->whereRaw('LOWER(drop_point) LIKE ?', ['%' . $toLower . '%']);
                        })
                        ->whereRaw('DATE(date_time) = ?', [$departingDate]) // IMPORTANT FIX
                        ->where('total_seats', '>=', $passengers)
                        ->where('date_time', '>', now())
                        ->orderBy('date_time', 'asc')
                        ->get();

            // DEBUG: Raw SQL check
            $query = Ride::with(['car', 'car.user'])
                        ->where(function($query) use ($from) {
                            $fromLower = strtolower($from);
                            $query->whereRaw('LOWER(pickup_point) LIKE ?', ['%' . $fromLower . '%']);
                        })
                        ->where(function($query) use ($to) {
                            $toLower = strtolower($to);
                            $query->whereRaw('LOWER(drop_point) LIKE ?', ['%' . $toLower . '%']);
                        })
                        ->whereRaw('DATE(date_time) = ?', [$departingDate])
                        ->where('total_seats', '>=', $passengers)
                        ->where('date_time', '>', now());

            Log::info('=== SQL QUERY ===');
            Log::info($query->toSql());
            Log::info('Bindings: ', $query->getBindings());

            $rides = $query->get();

            Log::info('=== FOUND RIDES COUNT ===');
            Log::info('Count: ' . $rides->count());

            if ($rides->isEmpty()) {
                // Check why no results
                $fromCount = Ride::whereRaw('LOWER(pickup_point) LIKE ?', ['%' . strtolower($from) . '%'])->count();
                $toCount = Ride::whereRaw('LOWER(drop_point) LIKE ?', ['%' . strtolower($to) . '%'])->count();
                $dateCount = Ride::whereRaw('DATE(date_time) = ?', [$departingDate])->count();
                
                Log::info('DEBUG - From matches: ' . $fromCount);
                Log::info('DEBUG - To matches: ' . $toCount);
                Log::info('DEBUG - Date matches: ' . $dateCount);
                Log::info('DEBUG - Searching date: ' . $departingDate);

                return response()->json([
                    'status' => true,
                    'message' => 'No rides found for your search criteria',
                    'data' => [],
                    'search_criteria' => [
                        'from' => $from,
                        'to' => $to,
                        'departing' => $departingDate,
                        'passengers' => $passengers
                    ],
                    'debug_info' => [
                        'from_matches' => $fromCount,
                        'to_matches' => $toCount,
                        'date_matches' => $dateCount
                    ]
                ]);
            }

            Log::info('=== RIDES FOUND SUCCESSFULLY ===');
            return response()->json([
                'status' => true,
                'message' => 'Rides found successfully',
                'data' => $rides,
                'search_criteria' => [
                    'from' => $from,
                    'to' => $to,
                    'departing' => $departingDate,
                    'passengers' => $passengers
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('=== SEARCH ERROR ===');
            Log::error('Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function flexibleSearch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'from' => 'required|string|max:255',
                'to' => 'required|string|max:255',
                'departing' => 'sometimes|date',
                'passengers' => 'sometimes|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $query = Ride::with(['car', 'car.user'])
                        ->where('pickup_point', $request->from) // Exact match
                        ->where('drop_point', $request->to)     // Exact match
                        ->where('date_time', '>', now()); // Only future rides

            // Add date filter if provided
            if ($request->has('departing') && $request->departing) {
                $departingDate = Carbon::parse($request->departing);
                $startOfDay = $departingDate->startOfDay()->toDateTimeString();
                $endOfDay = $departingDate->endOfDay()->toDateTimeString();
                $query->whereBetween('date_time', [$startOfDay, $endOfDay]);
            }

            // Add passengers filter if provided
            if ($request->has('passengers') && $request->passengers) {
                $query->where('total_seats', '>=', $request->passengers);
            }

            $rides = $query->orderBy('date_time', 'asc')->get();

            if ($rides->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No rides found for your search criteria',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Rides found successfully',
                'data' => $rides,
                'search_criteria' => $request->all()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTripDetails($id)
    {
        try {
            // Find the ride with all related data - SIMPLIFIED QUERY
            $ride = Ride::with([
                'car',
                'car.user:id,name,profile_picture,phone,email,rating,total_rides',
            ])->find($id);

            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Trip not found'
                ], 404);
            }

            // Calculate available seats
            $bookedSeats = \App\Models\Booking::where('ride_id', $id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum('seats_booked');
            
            $availableSeats = $ride->total_seats - $bookedSeats;

            // Format the response
            $tripDetails = [
                'trip_summary' => [
                    'ride_id' => $ride->id,
                    'date' => Carbon::parse($ride->date_time)->format('D, M d'),
                    'departure_time' => Carbon::parse($ride->date_time)->format('h:i A'),
                    'pickup_point' => $ride->pickup_point,
                    'pickup_location' => $ride->pickup_point . ', India',
                    'arrival_time' => Carbon::parse($ride->date_time)->addMinutes(90)->format('h:i A'),
                    'drop_point' => $ride->drop_point,
                    'drop_location' => $ride->drop_point . ', India',
                    'duration' => '1 hr 30 min',
                    'distance' => '25 km',
                ],
                'ride_information' => [
                    'total_seats' => $ride->total_seats,
                    'available_seats' => $availableSeats,
                    'price_per_seat' => '₹' . number_format($ride->price_per_seat, 2),
                    'total_price_2_seats' => '₹' . number_format($ride->price_per_seat * 2, 2),
                    'luggage_allowed' => $ride->luggage_allowed ? 'Yes' : 'No',
                    'luggage_restrictions' => $ride->luggage_allowed ? 
                        'Medium size luggage allowed (max 15kg)' : 
                        'No luggage allowed',
                ],
                'driver_info' => [
                    'name' => $ride->car->user->name ?? 'Driver',
                    'profile_picture' => $ride->car->user->profile_picture ?? '/default-avatar.png',
                    'phone' => $ride->car->user->phone ?? 'Not available',
                    'email' => $ride->car->user->email ?? 'Not available',
                    'rating' => $ride->car->user->rating ?? 4.5,
                    'total_rides' => $ride->car->user->total_rides ?? 50,
                    'verification_status' => $ride->car->license_verified === 'verified' ? 
                        'Verified Profile - Rarely Cancels Rides' : 
                        'Unverified Profile',
                    'driver_note' => "I'm a friendly driver and enjoy chatting with passengers. Feel free to ask me anything about the city!",
                    'languages' => ['Hindi', 'English'],
                    'member_since' => Carbon::parse($ride->car->user->created_at ?? now())->format('M Y'),
                ],
                'car_details' => [
                    'make' => $ride->car->car_make,
                    'model' => $ride->car->car_model,
                    'year' => $ride->car->car_year,
                    'color' => $ride->car->car_color,
                    'license_plate' => $ride->car->licence_plate,
                    'photo' => $ride->car->car_photo ?? '/default-car.jpg',
                    'license_verified' => $ride->car->license_verified,
                    'verification_notes' => $ride->car->verification_notes,
                ],
                'pickup_drop_preferences' => [
                    'pickup_from_home' => true,
                    'pickup_notes' => 'Changes as per requirement',
                    'drop_at_home' => true,
                    'drop_notes' => 'Changes as per requirement',
                ],
                'booking_policy' => [
                    'approval_required' => true,
                    'smoking_allowed' => false,
                    'pets_allowed' => false,
                    'music_allowed' => true,
                    'ac_available' => true,
                    'additional_rules' => [
                        'No smoking',
                        'No pets',
                        'Driver approval required',
                        'Be punctual',
                        'Carry ID proof',
                    ]
                ],
                'contact_options' => [
                    'can_contact_driver' => true,
                    'share_ride_option' => true,
                    'emergency_contact' => '+91 9876543210',
                ],
                'additional_features' => [
                    'has_wifi' => false,
                    'has_charging_port' => true,
                    'has_camera' => true,
                    'safety_rating' => 4.5,
                    'insurance_coverage' => 'Yes',
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Trip details retrieved successfully',
                'data' => $tripDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Trip details error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Seat Layout API for "Book Your Preferred Seat" screen
    public function getRideSeats($id)
    {
        try {
            $ride = Ride::find($id);

            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found'
                ], 404);
            }

            // Count booked seats
            $bookedCount = \App\Models\Booking::where('ride_id', $id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum('seats_booked');
            
            $totalSeats = $ride->total_seats;
            $availableSeats = $totalSeats - $bookedCount;
            
            // Generate virtual seat layout
            // Logic: Fills seats sequentially from 1 to Total. 
            // If total=4, booked=1 => [Seat 1: Booked, Seat 2: Available, Seat 3: Available, Seat 4: Available]
            
            $seats = [];
            for ($i = 1; $i <= $totalSeats; $i++) {
                $status = ($i <= $bookedCount) ? 'booked' : 'available';
                
                // Determine seat position (Mock logic for visual)
                // Assuming standard 4-seater: 1 (Front Left), 2 (Back Left), 3 (Back Middle), 4 (Back Right)
                // Just for fun/API richness
                $position = 'Back Seat';
                if ($i == 1) $position = 'Front Seat';
                
                $seats[] = [
                    'seat_number' => $i,
                    'status' => $status, // 'available', 'booked', 'selected' (client side)
                    'price' => $ride->price_per_seat,
                    'position' => $position
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Ride seats retrieved successfully',
                'data' => [
                    'ride_id' => $ride->id,
                    'total_seats' => $totalSeats,
                    'available_seats_count' => $availableSeats,
                    'currency' => '₹',
                    'price_per_seat' => $ride->price_per_seat,
                    'seats' => $seats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Remove the problematic helper methods or fix them:
    private function extractLocationDetails($location)
    {
        // Simple implementation
        $parts = explode(',', $location);
        if (count($parts) > 1) {
            return trim($parts[count($parts) - 1]) . ', India';
        }
        return $location . ', India';
    }

    private function calculateDistance($from, $to)
    {
        // For now return static value
        return '25 km';
    }

    private function calculateCancellationRate($driverId)
    {
        // Simple implementation
        return 'Rarely Cancels Rides';
    }


    // admin dashboard ride functions

    public function rides_list()
    {
        $rides = Ride::with(['car.user']) // Updated to use car.user instead of car.owner
                    ->latest()
                    ->get();
        
        $totalRides = $rides->count();
        $activeRides = $rides->where('status', 'active')->count();
        $completedRides = $rides->where('status', 'completed')->count();
        $cancelledRides = $rides->where('status', 'cancelled')->count();

        return view('admin.rides.index', compact(
            'rides', 
            'totalRides', 
            'activeRides', 
            'completedRides', 
            'cancelledRides'
        ));
    }

    // public function show_rides(Ride $ride)
    // {
    //     $ride->load(['driver', 'passenger']);
    //     return view('admin.rides.show', compact('ride'));
    // }
    public function show_rides($id)
    {
        // Get ride with all relationships
        $ride = Ride::with([
            'driver',   
            'car',
            'bookings.user'
        ])->findOrFail($id);
        
        // Calculate statistics
        $totalBookings = $ride->bookings->count();
        $confirmedBookings = $ride->bookings->where('status', 'confirmed')->count();
        $totalRevenue = $ride->bookings->where('status', 'confirmed')->sum('total_price');
        $availableSeats = $ride->availableSeats();
        
        return view('admin.rides.show', compact(
            'ride', 
            'totalBookings',
            'confirmedBookings',
            'totalRevenue',
            'availableSeats'
        ));
    }

    public function updateStatus(Request $request, Ride $ride)
    {
        $request->validate([
            'status' => 'required|in:active,completed,cancelled'
        ]);

        $ride->update(['status' => $request->status]);

        return redirect()->route('admin.rides.index')
                        ->with('success', 'Ride status updated successfully.');
    }

    public function destroy_rides(Ride $ride)
    {
        $ride->delete();

        return redirect()->route('admin.rides.index')
                        ->with('success', 'Ride deleted successfully.');
    }

    public function getDriverDetails($rideId)
    {
        $ride = Ride::with(['driver', 'car'])->find($rideId);
        
        if (!$ride) {
            return response()->json([
                'success' => false,
                'message' => 'Ride not found'
            ], 404);
        }
        
        $eta = $this->calculateETA($ride->driver_id, $ride->pickup_location);
        
        return response()->json([
            'success' => true,
            'driver' => [
                'id' => $ride->driver->id,
                'name' => $ride->driver->name,
                'phone' => $ride->driver->phone,
                'profile_picture' => $ride->driver->profile_picture,
                'rating' => $ride->driver->rating,
                'eta' => $eta . ' min'
            ],
            'car' => [
                'model' => $ride->car->model,
                'color' => $ride->car->color,
                'license_plate' => $ride->car->license_plate
            ]
        ]);
    }


    public function getDriverETA($driverId)
    {
        // This is a simplified version
        // In real app, you would calculate based on driver's current location
        $eta = 5; // Default 5 minutes
        
        return response()->json([
            'success' => true,
            'eta' => $eta,
            'unit' => 'min'
        ]);
    }

}
