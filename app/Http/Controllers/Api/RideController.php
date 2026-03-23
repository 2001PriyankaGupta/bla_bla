<?php
// app/Http/Controllers/Api/RideController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Notification;
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

            $rides = Ride::with(['car', 'car.user', 'bookings'])
                        ->whereHas('car', function($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->latest()
                        ->get()
                        ->map(function($ride) {
                            $bookedSeats = $ride->bookings
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->sum('seats_booked');
                            $ride->booked_seats    = (int) $bookedSeats;
                            $ride->available_seats = max(0, $ride->total_seats - (int) $bookedSeats);
                            return $ride;
                        });

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

   public function active_rides(Request $request)
    {
        try {
            // Debug log
            Log::info('Active rides endpoint accessed');
            
            // Authenticate user
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to view rides.'
                ], 401);
            }

            Log::info('User authenticated: ' . $user->id);

            // Get current time
            $now = now();
            Log::info('Current time: ' . $now);

            // Query active rides - sirf status 'active' aur future wale
            $rides = Ride::with(['car', 'car.user'])
                        ->whereHas('car', function($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->where('status', 'active') // sirf active status
                        ->where('date_time', '>', $now) // sirf future rides
                        ->orderBy('date_time', 'asc')
                        ->get();

            Log::info('Found ' . $rides->count() . ' active rides');

            // Format rides
            $formattedRides = $rides->map(function($ride) {
                return [
                    'id' => $ride->id,
                    'from' => $ride->pickup_point,
                    'to' => $ride->drop_point,
                    'date_time' => $ride->date_time->format('Y-m-d H:i:s'),
                    'seats' => $ride->total_seats,
                    'price' => $ride->price, // Price per seat? Wait, ride table has price_per_seat usually.
                    'price_per_seat' => $ride->price_per_seat, 
                    'status' => $ride->status,
                    'car' => $ride->car ? [
                        'model' => $ride->car->car_model,
                        'license_plate' => $ride->car->licence_plate,
                    ] : null
                ];
            });

            // Calculate Earnings Summary
            // Today's Earnings
            $todayEarnings = Booking::whereHas('ride.car', function($q) use ($user) {
                                    $q->where('user_id', $user->id);
                                })
                                ->whereIn('status', ['confirmed', 'completed'])
                                ->whereDate('created_at', Carbon::today())
                                ->sum('total_price');

            // This Week's Earnings
            $weekEarnings = Booking::whereHas('ride.car', function($q) use ($user) {
                                    $q->where('user_id', $user->id);
                                })
                                ->whereIn('status', ['confirmed', 'completed'])
                                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                ->sum('total_price');

            return response()->json([
                'status' => true,
                'message' => 'Active rides and earnings fetched successfully',
                'data' => $formattedRides,
                'count' => $rides->count(),
                'earnings' => [
                    'today' => (float)$todayEarnings,
                    'week' => (float)$weekEarnings
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in active_rides: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch active rides: ' . $e->getMessage()
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
                'stop_points' => 'nullable|array',
                'stop_points.*.city_name' => 'required_with:stop_points|string',
                'stop_points.*.price' => 'required_with:stop_points|numeric|min:0',
                'total_seats' => 'required|integer|min:1',
                'price_per_seat' => 'required|numeric|min:0',
                'car_make' => 'required|string|exists:cars,car_make,user_id,' . $user->id, // Fix typo
                'luggage_allowed' => 'required|boolean',
                'status' => 'sometimes|in:active,inactive'
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

            // 🔒 LICENSE VERIFICATION CHECK
            // Only cars with admin-verified license can be used to create rides
            if ($car->license_verified !== 'verified') {
                $statusMessages = [
                    'pending'  => 'Your car\'s license is still under review by admin. Please wait for approval before offering a ride.',
                    'rejected' => 'Your car\'s license has been rejected by admin. Please contact support or update your documents.',
                ];
                $msg = $statusMessages[$car->license_verified]
                    ?? 'Your car\'s license is not verified. Only verified cars can be used to offer rides.';

                return response()->json([
                    'status'            => false,
                    'error'             => $msg,
                    'verification_status' => $car->license_verified,
                ], 403);
            }

            $ride = Ride::create([
                'pickup_point' => $request->pickup_point,
                'drop_point' => $request->drop_point,
                'date_time' => $request->date_time,
                'total_seats' => $request->total_seats,
                'price_per_seat' => $request->price_per_seat,
                'car_id' => $car->id,
                'luggage_allowed' => $request->luggage_allowed,
                'status' => $request->status ?? 'active',
            ]);
            
            // Handle Stop Points
            if ($request->has('stop_points')) {
                foreach ($request->stop_points as $index => $stop) {
                    $ride->stopPoints()->create([
                        'city_name' => $stop['city_name'],
                        'price_from_pickup' => $stop['price'],
                        'sequence' => $index + 1
                    ]);
                }
            }

            // Notify Admin about new ride creation
            $admins = \App\Models\User::where('is_admin', 1)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'New Ride Posted',
                    'message' => "{$user->name} has posted a new ride from {$ride->pickup_point} to {$ride->drop_point}.",
                    'type'    => 'new_ride_posted',
                    'data'    => [
                        'ride_id' => $ride->id,
                        'user_id' => $user->id,
                        'route'   => "{$ride->pickup_point} -> {$ride->drop_point}"
                    ]
                ]);
            }

            $ride->load(['car', 'stopPoints']);
            
            return response()->json([
                'status' => true,
                'message' => 'Ride created successfully',
                'data' => $ride
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

            $ride = Ride::with(['car', 'car.user', 'stopPoints'])
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
                'stop_points' => 'nullable|array',
                'stop_points.*.city_name' => 'required_with:stop_points|string',
                'stop_points.*.price' => 'required_with:stop_points|numeric|min:0',
                'total_seats' => 'sometimes|integer|min:1',
                'price_per_seat' => 'sometimes|numeric|min:0',
                'car_make' => 'sometimes|string|exists:cars,car_make,user_id,' . $user->id, // sometimes for update
                'luggage_allowed' => 'sometimes|boolean',
                'status' => 'sometimes|in:active,inactive'
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
                'status' => $request->status ?? $ride->status,
            ]);
            
            // Update Stop Points
            if ($request->has('stop_points')) {
                // Delete existing ones
                $ride->stopPoints()->delete();
                // Add new ones
                foreach ($request->stop_points as $stop) {
                    $ride->stopPoints()->create([
                        'city_name' => $stop['city_name'],
                        'price_from_pickup' => $stop['price']
                    ]);
                }
            }
            
            $ride->load(['car', 'stopPoints']);

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
                        'date_time' => \Carbon\Carbon::parse($rideData->date_time)->format('Y-m-d H:i:s')
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

            // Compare only date (ignore time)
            $departingDate = Carbon::parse($departing)->toDateString();

            Log::info('=== SEARCH PARAMETERS ===');
            Log::info('From: ' . $from);
            Log::info('To: ' . $to);
            Log::info('Date: ' . $departingDate);
            Log::info('Passengers: ' . $passengers);

            // Get all candidate rides
            $allRides = Ride::with(['car', 'car.user', 'stopPoints'])
                ->whereRaw('DATE(date_time) = ?', [$departingDate])
                ->where('date_time', '>', now())
                ->where('status', 'active')
                ->get();

            $matchingRides = [];

            foreach ($allRides as $ride) {
                $route = $ride->getFullRoute();
                $cityNames = array_map(fn($r) => strtolower(trim($r['name'])), $route);
                
                $searchFrom = strtolower(trim($from));
                $searchTo = strtolower(trim($to));

                // Find indices of from and to in the route
                $fromIdx = -1;
                $toIdx = -1;

                foreach ($cityNames as $idx => $cityName) {
                    if (str_contains($cityName, $searchFrom)) $fromIdx = $idx;
                    if (str_contains($cityName, $searchTo)) $toIdx = $idx;
                }

                // Sequence Check: From must exist, To must exist, and To must be after From
                if ($fromIdx !== -1 && $toIdx !== -1 && $toIdx > $fromIdx) {
                    
                    // 1. Calculate price for this specific segment
                    $priceFrom = $route[$fromIdx]['price'];
                    $priceTo = $route[$toIdx]['price'];
                    $segmentPrice = max(0, $priceTo - $priceFrom);

                    // 2. Check seat availability for this segment
                    $available = $ride->availableSeats($route[$fromIdx]['name'], $route[$toIdx]['name']);

                    if ($available >= $passengers) {
                        $rideData = $ride->toArray();
                        
                        // Calculate availability for each potential drop point FROM the search pickup
                        $searchFromCity = $route[$fromIdx]['name'];
                        $segmentAvailabilities = [];
                        
                        foreach ($route as $idx => $stop) {
                            if ($idx > $fromIdx) {
                                $segmentAvailabilities[] = [
                                    'city_name' => $stop['name'],
                                    'available_seats' => $ride->availableSeats($searchFromCity, $stop['name']),
                                    'price' => max(0, $stop['price'] - $route[$fromIdx]['price'])
                                ];
                            }
                        }

                        // 🚀 CRITICAL: Override top-level data for segment-specific UI
                        $rideData['available_seats'] = $available; 
                        $rideData['booked_seats'] = $ride->total_seats - $available;
                        $rideData['price_per_seat'] = (float)$segmentPrice;
                        $rideData['stop_availabilities'] = $segmentAvailabilities;
                        
                        $rideData['search_segment'] = [
                            'pickup' => $route[$fromIdx]['name'],
                            'drop' => $route[$toIdx]['name'],
                            'price' => (float)$segmentPrice,
                            'available_seats' => $available
                        ];
                        
                        $matchingRides[] = $rideData;
                    }
                }
            }

            Log::info('=== FOUND RIDES COUNT ===');
            Log::info('Count: ' . count($matchingRides));

            return response()->json([
                'status' => true,
                'message' => count($matchingRides) > 0 ? 'Rides found successfully' : 'No rides found for your search criteria',
                'data' => $matchingRides,
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

    public function getLocationSuggestions(Request $request)
    {
        $query = $request->query('query', '');
        
        // Get unique cities from pickup_point, drop_point and stop_points
        $pickupCities = Ride::where('pickup_point', 'LIKE', "%{$query}%")
            ->where('status', 'active')
            ->distinct()
            ->pluck('pickup_point')
            ->toArray();

        $dropCities = Ride::where('drop_point', 'LIKE', "%{$query}%")
            ->where('status', 'active')
            ->distinct()
            ->pluck('drop_point')
            ->toArray();

        $stopCities = \DB::table('stop_points')
            ->where('city_name', 'LIKE', "%{$query}%")
            ->distinct()
            ->pluck('city_name')
            ->toArray();

        $allCities = array_unique(array_merge($pickupCities, $dropCities, $stopCities));
        
        $formatted = array_map(function($city) {
            return [
                'description' => $city,
                'is_active_route' => true
            ];
        }, array_values($allCities));

        return response()->json([
            'status' => true,
            'predictions' => array_slice($formatted, 0, 10)
        ]);
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

            // Extract keywords for broader matching
            $fromParts = explode(',', $request->from);
            $fromKeyword = trim($fromParts[0]);
            
            $toParts = explode(',', $request->to);
            $toKeyword = trim($toParts[0]);

            $query = Ride::with(['car', 'car.user', 'stopPoints'])
                        ->where('status', 'active')
                        ->where('date_time', '>', now());

            $query->where(function($q) use ($fromKeyword, $toKeyword) {
                $q->where(function($qq) use ($fromKeyword, $toKeyword) {
                    // Match main pickup and main drop
                    $qq->where('pickup_point', 'LIKE', "%$fromKeyword%")
                       ->where('drop_point', 'LIKE', "%$toKeyword%");
                })
                ->orWhere(function($qq) use ($fromKeyword, $toKeyword) {
                    // Match main pickup and a stop point
                    $qq->where('pickup_point', 'LIKE', "%$fromKeyword%")
                       ->whereHas('stopPoints', function($sq) use ($toKeyword) {
                           $sq->where('city_name', 'LIKE', "%$toKeyword%");
                       });
                })
                ->orWhere(function($qq) use ($fromKeyword, $toKeyword) {
                    // Match a stop point and main drop point
                    $qq->where('drop_point', 'LIKE', "%$toKeyword%")
                       ->whereHas('stopPoints', function($sq) use ($fromKeyword) {
                           $sq->where('city_name', 'LIKE', "%$fromKeyword%");
                       });
                })
                ->orWhere(function($qq) use ($fromKeyword, $toKeyword) {
                    // Match two different stop points
                    $qq->whereHas('stopPoints', function($sq) use ($fromKeyword) {
                        $sq->where('city_name', 'LIKE', "%$fromKeyword%");
                    })->whereHas('stopPoints', function($sq) use ($toKeyword) {
                        $sq->where('city_name', 'LIKE', "%$toKeyword%");
                    });
                });
            });

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

    // public function getTripDetails($id)
    // {
    //     try {
    //         // Pehle booking find karein
    //         $booking = \App\Models\Booking::with([
    //             'ride',
    //             'ride.car',
    //             'ride.car.user:id,name,profile_picture,phone,email,rating,total_rides',
    //         ])->find($id);

    //         if (!$booking) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Booking not found'
    //             ], 404);
    //         }

    //         $ride = $booking->ride;

    //         if (!$ride) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Ride not found for this booking'
    //             ], 404);
    //         }

    //         // Calculate available seats for this ride
    //         $bookedSeats = \App\Models\Booking::where('ride_id', $ride->id)
    //             ->whereIn('status', ['pending', 'confirmed'])
    //             ->sum('seats_booked');
            
    //         $availableSeats = $ride->total_seats - $bookedSeats;

    //         // Format the response with booking information
    //         $tripDetails = [
    //             'trip_summary' => [
    //                 'booking_id' => $booking->id,
    //                 'booking_status' => $booking->status,
    //                 'ride_id' => $ride->id,
    //                 'date' => Carbon::parse($ride->date_time)->format('D, M d'),
    //                 'departure_time' => Carbon::parse($ride->date_time)->format('h:i A'),
    //                 'pickup_point' => $ride->pickup_point,
    //                 'pickup_location' => $ride->pickup_point . ', India',
    //                 'arrival_time' => Carbon::parse($ride->date_time)->addMinutes(90)->format('h:i A'),
    //                 'drop_point' => $ride->drop_point,
    //                 'drop_location' => $ride->drop_point . ', India',
    //                 'duration' => '1 hr 30 min',
    //                 'distance' => '25 km',
    //             ],
    //             'booking_information' => [
    //                 'booking_reference' => 'BK-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
    //                 'seats_booked' => $booking->seats_booked,
    //                 'total_price' => '₹' . number_format($booking->total_price, 2),
    //                 'booking_status' => ucfirst($booking->status),
    //                 'booking_date' => Carbon::parse($booking->created_at)->format('M d, Y h:i A'),
    //                 'special_requests' => $booking->special_requests ?? 'No special requests',
    //                 'approved_at' => $booking->approved_at ? 
    //                     Carbon::parse($booking->approved_at)->format('M d, Y h:i A') : 
    //                     'Not approved yet',
    //                 'rejection_reason' => $booking->rejection_reason ?? 'N/A',
    //             ],
    //             'ride_information' => [
    //                 'total_seats' => $ride->total_seats,
    //                 'available_seats' => $availableSeats,
    //                 'price_per_seat' => '₹' . number_format($ride->price, 2),
    //                 'your_total_price' => '₹' . number_format($booking->total_price, 2),
    //                 'luggage_allowed' => $ride->luggage_allowed ? 'Yes' : 'No',
    //                 'luggage_restrictions' => $ride->luggage_allowed ? 
    //                     'Medium size luggage allowed (max 15kg)' : 
    //                     'No luggage allowed',
    //                 'ride_status' => $ride->status,
    //             ],
    //             'driver_info' => [
    //                 'name' => $ride->car->user->name ?? 'Driver',
    //                 'profile_picture' => $ride->car->user->profile_picture ?? '/default-avatar.png',
    //                 'phone' => $ride->car->user->phone ?? 'Not available',
    //                 'email' => $ride->car->user->email ?? 'Not available',
    //                 'rating' => $ride->car->user->rating ?? 4.5,
    //                 'total_rides' => $ride->car->user->total_rides ?? 50,
    //                 'verification_status' => $ride->car->license_verified === 'verified' ? 
    //                     'Verified Profile - Rarely Cancels Rides' : 
    //                     'Unverified Profile',
    //                 'driver_note' => "I'm a friendly driver and enjoy chatting with passengers. Feel free to ask me anything about the city!",
    //                 'languages' => ['Hindi', 'English'],
    //                 'member_since' => Carbon::parse($ride->car->user->created_at ?? now())->format('M Y'),
    //             ],
    //             'car_details' => [
    //                 'make' => $ride->car->car_make,
    //                 'model' => $ride->car->car_model,
    //                 'year' => $ride->car->car_year,
    //                 'color' => $ride->car->car_color,
    //                 'license_plate' => $ride->car->licence_plate,
    //                 'photo' => $ride->car->car_photo ?? '/default-car.jpg',
    //                 'license_verified' => $ride->car->license_verified,
    //                 'verification_notes' => $ride->car->verification_notes,
    //             ],
    //             'pickup_drop_preferences' => [
    //                 'pickup_from_home' => true,
    //                 'pickup_notes' => 'Changes as per requirement',
    //                 'drop_at_home' => true,
    //                 'drop_notes' => 'Changes as per requirement',
    //             ],
    //             'booking_policy' => [
    //                 'approval_required' => true,
    //                 'smoking_allowed' => false,
    //                 'pets_allowed' => false,
    //                 'music_allowed' => true,
    //                 'ac_available' => true,
    //                 'additional_rules' => [
    //                     'No smoking',
    //                     'No pets',
    //                     'Driver approval required',
    //                     'Be punctual',
    //                     'Carry ID proof',
    //                 ]
    //             ],
    //             'contact_options' => [
    //                 'can_contact_driver' => true,
    //                 'share_ride_option' => true,
    //                 'emergency_contact' => '+91 9876543210',
    //             ],
    //             'additional_features' => [
    //                 'has_wifi' => false,
    //                 'has_charging_port' => true,
    //                 'has_camera' => true,
    //                 'safety_rating' => 4.5,
    //                 'insurance_coverage' => 'Yes',
    //             ],
    //             'booking_timeline' => [
    //                 'booked_on' => Carbon::parse($booking->created_at)->format('M d, Y h:i A'),
    //                 'approved_on' => $booking->approved_at ? 
    //                     Carbon::parse($booking->approved_at)->format('M d, Y h:i A') : 
    //                     'Pending',
    //                 'ride_date' => Carbon::parse($ride->date_time)->format('M d, Y'),
    //             ]
    //         ];

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Trip details with booking information retrieved successfully',
    //             'data' => $tripDetails
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Trip details error: ' . $e->getMessage());
    //         Log::error('Stack trace: ' . $e->getTraceAsString());
            
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Server error: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getTripDetails($id)
    {
        try {
            // Check if ID is for Booking or Ride
        $booking = \App\Models\Booking::with([
            'ride',
            'ride.car',
            'ride.car.user:id,name,profile_picture,phone,email,rating,total_rides',
            'user:id,name,profile_picture,phone,email,gender',
            'payment'
        ])->find($id);

        $ride = null;
        if ($booking) {
            $ride = $booking->ride;
        } else {
            // If not a booking ID, maybe it's a Ride ID
            $ride = \App\Models\Ride::with([
                'car',
                'car.user:id,name,profile_picture,phone,email,rating,total_rides',
                'stopPoints'
            ])->find($id);
            
            if (!$ride) {
                return response()->json(['status' => false, 'message' => 'Trip not found'], 404);
            }
        }

        // Determine pickup and drop points based on context (driver vs passenger)
        $displayPickup = $booking ? ($booking->pickup_point ?? $ride->pickup_point) : $ride->pickup_point;
        $displayDrop = $booking ? ($booking->drop_point ?? $ride->drop_point) : $ride->drop_point;

        // Calculate available seats... 
        $availableSeats = $ride->availableSeats($displayPickup, $displayDrop);

            // Get co-passengers for this ride (excluding current passenger)
            $coPassengers = \App\Models\Booking::with(['user:id,name,profile_picture,gender'])
                ->where('ride_id', $ride->id)
                ->when($booking, function ($query) use ($booking) { // Only exclude if it's a booking context
                    return $query->where('id', '!=', $booking->id);
                })
                ->whereIn('status', ['pending', 'confirmed'])
                ->get()
                ->map(function($coBooking) {
                    return [
                        'name' => $coBooking->user->name ?? 'Passenger',
                        'profile_picture' => $coBooking->user->profile_picture ?? '/default-avatar.png',
                        'gender' => $coBooking->user->gender ?? 'Not specified',
                        'seats_booked' => $coBooking->seats_booked,
                        'booking_status' => $coBooking->status,
                    ];
                });

            // Check if current user has already reviewed
            $hasReviewed = false;
            $myReview = null;
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user) {
                    $myReview = \App\Models\Review::where('booking_id', $booking->id)
                        ->where('reviewer_id', $user->id)
                        ->first();
                    $hasReviewed = $myReview ? true : false;
                }
            } catch (\Exception $e) {
                // Not logged in or invalid token, ignore
            }

            // Format the response with booking information
            $tripDetails = [
                'trip_summary' => [
                    'booking_id' => $booking ? $booking->id : null,
                    'booking_status' => $booking ? $booking->status : null,
                    'ride_id' => $ride->id,
                    'date' => Carbon::parse($ride->date_time)->format('d-M-Y'),
                    'departure_time' => Carbon::parse($ride->date_time)->format('h:i A'),
                    'pickup_point' => $displayPickup,
                    'pickup_location' => $displayPickup . ($booking ? '' : ', India'),
                    'arrival_time' => Carbon::parse($ride->date_time)->addMinutes(90)->format('h:i A'),
                    'drop_point' => $displayDrop,
                    'drop_location' => $displayDrop . ($booking ? '' : ', India'),
                    'duration' => 'Calculated...',
                    'distance' => 'Calculated...',
                    'available_seats' => $availableSeats,
                    'total_seats' => $ride->total_seats,
                    'price_per_seat' => $ride->price_per_seat,
                ],
                'stop_availabilities' => collect($ride->getFullRoute())->map(function($stop) use ($ride, $displayPickup) {
                    return [
                        'city_name' => $stop['name'],
                        'available_seats' => $ride->availableSeats($displayPickup, $stop['name']),
                        'price' => (float)$stop['price']
                    ];
                })->filter(function($stop) use ($ride, $displayPickup) {
                    $route = $ride->getFullRoute();
                    $cityNames = array_map(fn($r) => strtolower(trim(explode(',', $r['name'])[0])), $route);
                    $fromIdx = array_search(strtolower(trim(explode(',', $displayPickup)[0])), $cityNames);
                    $toIdx = array_search(strtolower(trim(explode(',', $stop['city_name'])[0])), $cityNames);
                    return $fromIdx !== false && $toIdx !== false && $toIdx > $fromIdx;
                })->values(),
                'booking_information' => [
                    'booking_reference' => 'BK-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                    'seats_booked' => $booking->seats_booked,
                    'total_price' => '₹' . number_format($booking->total_price, 2),
                    'booking_status' => ucfirst($booking->status),
                    'booking_date' => Carbon::parse($booking->created_at)->format('d-M-Y, h:i A'),
                    'special_requests' => $booking->special_requests ?? 'No special requests',
                    'approved_at' => $booking->approved_at ? 
                        Carbon::parse($booking->approved_at)->format('d-M-Y, h:i A') : 
                        'Not approved yet',
                    'rejection_reason' => $booking->rejection_reason ?? 'N/A',
                    'has_reviewed' => $hasReviewed,
                    'my_review' => $myReview ? [
                        'rating' => $myReview->rating,
                        'comment' => $myReview->comment,
                        'created_at' => $myReview->created_at->format('d-M-Y')
                    ] : null
                ],
                
                // PASSENGER INFORMATION SECTION
                'passenger_info' => [
                    'id' => $booking->user_id,
                    'name' => $booking->user->name ?? 'Passenger',
                    'profile_picture' => $booking->user->profile_picture ?? '/default-avatar.png',
                    'phone' => $booking->user->phone ?? 'Not available',
                    'email' => $booking->user->email ?? 'Not available',
                    'gender' => $booking->user->gender ?? 'Not specified',
                    'age' => $booking->user->date_of_birth ? 
                        Carbon::parse($booking->user->date_of_birth)->age : 
                        'Not specified',
                    'emergency_contact' => $booking->user->emergency_contact ?? 'Not available',
                    'booking_role' => 'Primary Passenger',
                    'verification_status' => 'Verified Passenger',
                    'passenger_note' => "I'm a punctual passenger who respects driver's time and rules.",
                    'languages' => ['Hindi', 'English'],
                    'member_since' => Carbon::parse($booking->user->created_at ?? now())->format('M Y'),
                    'total_bookings' => \App\Models\Booking::where('user_id', $booking->user_id)
                        ->whereIn('status', ['completed'])
                        ->count(),
                ],
                
                // CO-PASSENGERS INFORMATION
                'co_passengers' => [
                    'total_co_passengers' => count($coPassengers),
                    'total_co_passenger_seats' => $coPassengers->sum('seats_booked'),
                    'list' => $coPassengers,
                ],
                
                'ride_information' => [
                    'total_seats' => $ride->total_seats,
                    'available_seats' => $availableSeats,
                    'price_per_seat' => '₹' . number_format($ride->price, 2),
                    'your_total_price' => '₹' . number_format($booking->total_price, 2),
                    'luggage_allowed' => $ride->luggage_allowed ? 'Yes' : 'No',
                    'luggage_restrictions' => $ride->luggage_allowed ? 
                        'Medium size luggage allowed (max 15kg)' : 
                        'No luggage allowed',
                    'ride_status' => $ride->status,
                ],
                'driver_info' => [
                    'id' => $ride->car->user->id ?? null,
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
                    'passenger_emergency_contact' => $booking->user->emergency_contact ?? 'Not available',
                ],
                'additional_features' => [
                    'has_wifi' => false,
                    'has_charging_port' => true,
                    'has_camera' => true,
                    'safety_rating' => 4.5,
                    'insurance_coverage' => 'Yes',
                ],
                'booking_timeline' => [
                    'booked_on' => Carbon::parse($booking->created_at)->format('M d, Y h:i A'),
                    'approved_on' => $booking->approved_at ? 
                        Carbon::parse($booking->approved_at)->format('M d, Y h:i A') : 
                        'Pending',
                    'ride_date' => Carbon::parse($ride->date_time)->format('M d, Y'),
                ],
                
                // PASSENGER RIDE PREFERENCES
                'passenger_preferences' => [
                    'preferred_seat' => 'Front Seat',
                    'preferred_temperature' => 'Moderate AC',
                    'preferred_conversation' => 'Casual',
                    'special_needs' => $booking->special_requests ?? 'None',
                    'food_allowed' => true,
                    'drinks_allowed' => true,
                ],
                
                // PAYMENT INFORMATION FOR PASSENGER
                'payment_info' => [
                    'payment_method' => $booking->payment ? ($booking->payment->payment_method ?? 'Online') : 'Cash',
                    'payment_status' => $booking->payment ? $booking->payment->status : 'Pending',
                    'amount_paid' => $booking->payment ? '₹' . number_format($booking->payment->amount, 2) : '₹0.00',
                    'amount_due' => $booking->payment ? '₹0.00' : '₹' . number_format($booking->total_price, 2),
                    'transaction_id' => $booking->payment ? $booking->payment->transaction_id : 'N/A',
                    'payment_date' => $booking->payment ? 
                        Carbon::parse($booking->payment->payment_date)->format('M d, Y h:i A') : 
                        'Not paid yet',
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Trip details with booking information retrieved successfully',
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
            'bookings.user',
            'stopPoints' // Load stop points
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
            'status' => 'required|in:active,completed,cancelled,inactive'
        ]);

        $ride->update(['status' => $request->status]);

        // Notify Driver
        if ($ride->driver) {
            Notification::create([
                'user_id' => $ride->driver->id,
                'title'   => 'Ride Status Updated by Admin',
                'message' => "Admin has updated the status of your ride from {$ride->pickup_point} to {$ride->drop_point} to '" . ucfirst($request->status) . "'.",
                'type'    => 'ride_status_update',
                'data'    => [
                    'ride_id' => $ride->id,
                    'status'  => $request->status,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Ride status updated successfully.');
    }

    public function create_ride()
    {
        $cars = Car::with('user')->get();
        return view('admin.rides.create', compact('cars'));
    }

    public function store_ride(Request $request)
    {
        $request->validate([
            'pickup_point' => 'required|string|max:255',
            'drop_point' => 'required|string|max:255',
            'date_time' => 'required|date',
            'total_seats' => 'required|integer|min:1',
            'price_per_seat' => 'required|numeric|min:0',
            'car_id' => 'required|exists:cars,id',
            'luggage_allowed' => 'required|boolean',
            'status' => 'required|string|in:active,completed,cancelled,inactive',
            'stop_points' => 'nullable|array',
            'stop_points.*.city_name' => 'required_with:stop_points|string|max:255',
            'stop_points.*.price_from_pickup' => 'required_with:stop_points|numeric|min:0'
        ]);

        $ride = Ride::create($request->all());

        if ($request->has('stop_points')) {
            foreach ($request->stop_points as $stopPointData) {
                if (!empty($stopPointData['city_name'])) {
                    $ride->stopPoints()->create([
                        'city_name' => $stopPointData['city_name'],
                        'price_from_pickup' => $stopPointData['price_from_pickup']
                    ]);
                }
            }
        }

        return redirect()->route('admin.rides.show', $ride->id)
                        ->with('success', 'Ride created successfully.');
    }

    public function edit_ride($id)
    {
        $ride = Ride::with('stopPoints')->findOrFail($id);
        $cars = Car::with('user')->get();
        return view('admin.rides.edit', compact('ride', 'cars'));
    }

    public function update_ride(Request $request, $id)
    {
        $ride = Ride::findOrFail($id);
        
        $request->validate([
            'pickup_point' => 'required|string|max:255',
            'drop_point' => 'required|string|max:255',
            'date_time' => 'required|date',
            'total_seats' => 'required|integer|min:1',
            'price_per_seat' => 'required|numeric|min:0',
            'car_id' => 'required|exists:cars,id',
            'luggage_allowed' => 'required|boolean',
            'status' => 'required|string|in:active,completed,cancelled,inactive',
            'stop_points' => 'nullable|array',
            'stop_points.*.city_name' => 'required_with:stop_points|string|max:255',
            'stop_points.*.price_from_pickup' => 'required_with:stop_points|numeric|min:0'
        ]);

        $ride->update($request->all());

        if ($request->has('stop_points')) {
            // Remove existing stop points if not in the current list
            $ride->stopPoints()->delete();
            
            // Add new stop points
            foreach ($request->stop_points as $stopPointData) {
                if (!empty($stopPointData['city_name'])) {
                    $ride->stopPoints()->create([
                        'city_name' => $stopPointData['city_name'],
                        'price_from_pickup' => $stopPointData['price_from_pickup']
                    ]);
                }
            }
        }

        // Notify Driver
        if ($ride->driver) {
            Notification::create([
                'user_id' => $ride->driver->id,
                'title'   => 'Ride Updated by Admin',
                'message' => "Admin has modified your ride details from {$ride->pickup_point} to {$ride->drop_point}. Please check the updated details.",
                'type'    => 'ride_updated',
                'data'    => [
                    'ride_id' => $ride->id,
                    'updated_fields' => array_keys($request->except(['_token', '_method'])),
                    'updated_at' => now()->toDateTimeString()
                ]
            ]);
        }

        return redirect()->route('admin.rides.show', $ride->id)
                        ->with('success', 'Ride updated successfully.');
    }

    public function sendReminder($id)
    {
        $ride = Ride::with('bookings.user')->findOrFail($id);
        // Include both confirmed and pending bookings for the reminder
        $passengers = $ride->bookings->whereIn('status', ['confirmed', 'pending'])->pluck('user')->filter();

        if ($passengers->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active passengers (confirmed or pending) found for this ride.'
            ], 404);
        }

        foreach ($passengers as $passenger) {
            Notification::create([
                'user_id' => $passenger->id,
                'title' => 'Ride Reminder',
                'message' => 'Reminder: Your ride from ' . $ride->pickup_point . ' to ' . $ride->drop_point . ' is scheduled for ' . $ride->date_time->format('M d, h:i A'),
                'type' => 'ride_reminder',
                'data' => ['ride_id' => $ride->id]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Reminder notifications sent to ' . $passengers->count() . ' passenger(s).'
        ]);
    }

    public function destroy_rides(Ride $ride)
    {
        // Notify Driver Before Deletion
        if ($ride->driver) {
            Notification::create([
                'user_id' => $ride->driver->id,
                'title'   => 'Ride Deleted by Admin',
                'message' => "Admin has deleted your ride from {$ride->pickup_point} to {$ride->drop_point} scheduled for {$ride->date_time->format('M d, Y h:i A')}.",
                'type'    => 'ride_deleted',
                'data'    => [
                    'ride_details' => [
                        'from' => $ride->pickup_point,
                        'to' => $ride->drop_point,
                        'date_time' => $ride->date_time->toDateTimeString()
                    ],
                    'deleted_at' => now()->toDateTimeString()
                ]
            ]);
        }

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
