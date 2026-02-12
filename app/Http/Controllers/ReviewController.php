<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    
    public function getDriverReviews($driverId)
    {
        $driver = User::find($driverId);
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found'
            ], 404);
        }

        $reviews = Review::where('driver_id', $driverId)
            ->where('type', 'driver')
            ->with(['passenger:id,name,profile_picture', 'reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->total();

        return response()->json([
            'success' => true,
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
                'profile_picture' => $driver->profile_picture
            ],
            'stats' => [
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $totalReviews,
                'rating_breakdown' => $this->getRatingBreakdown($driverId, 'driver')
            ],
            'reviews' => $reviews
        ]);
    }

   
    public function getUserReviews($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $reviews = Review::where('user_id', $userId)
            ->where('type', 'passenger')
            ->with(['driver:id,name,profile_picture', 'reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->total();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'profile_picture' => $user->profile_picture
            ],
            'stats' => [
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $totalReviews,
                'rating_breakdown' => $this->getRatingBreakdown($userId, 'passenger')
            ],
            'reviews' => $reviews
        ]);
    }

    public function getMyDriverReviews()
    {
        $user = Auth::user();

        $reviews = Review::where('driver_id', $user->id)
            ->where('type', 'driver')
            ->with(['passenger:id,name,profile_picture', 'reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->total();

        return response()->json([
            'success' => true,
            'stats' => [
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $totalReviews,
                'rating_breakdown' => $this->getRatingBreakdown($user->id, 'driver')
            ],
            'reviews' => $reviews
        ]);
    }

    
    public function getMyPassengerReviews()
    {
        $user = Auth::user();

        $reviews = Review::where('user_id', $user->id)
            ->where('type', 'passenger')
            ->with(['driver:id,name,profile_picture', 'reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->total();

        return response()->json([
            'success' => true,
            'stats' => [
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $totalReviews,
                'rating_breakdown' => $this->getRatingBreakdown($user->id, 'passenger')
            ],
            'reviews' => $reviews
        ]);
    }

    
    public function submitReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'type' => 'required|in:driver,passenger',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $booking = Booking::with('ride.car')->find($request->booking_id); // ✅ Added 'car' relation

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Check if ride is completed
        if ($booking->status != 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You can only review completed rides'
            ], 400);
        }

        // ✅ Get driver_id from car relation
        $driver_id = $booking->ride->car->user_id; // Car owner is driver
        
        if ($request->type == 'driver') {
            // Passenger reviewing driver
            if ($booking->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only review your own bookings as passenger'
                ], 403);
            }
            $reviewer_id = $booking->user_id; // Passenger is reviewer
        } else {
            // Driver reviewing passenger
            if ($driver_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only driver can review passengers'
                ], 403);
            }
            $reviewer_id = $driver_id; // Driver is reviewer
        }

        // Check if review already exists
        $existingReview = Review::where('booking_id', $request->booking_id)
            ->where('type', $request->type)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Review already submitted for this booking'
            ], 400);
        }

        // ✅ Create review - Fixed column mappings
        $reviewData = [
            'booking_id' => $request->booking_id,
            'type' => $request->type,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'driver_id' => $driver_id,           // Who is being reviewed as driver
            'user_id' => $booking->user_id,      // Who is being reviewed as passenger
            'reviewer_id' => $user->id,          // Who gave the review
        ];

        $review = Review::create($reviewData);

        // Update user ratings
        $this->updateUserRatings($driver_id, $booking->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review' => $review->load(['passenger:id,name', 'driver:id,name'])
        ], 201);
    }

    
    public function updateReview(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|between:1,5',
            'comment' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user is the reviewer
        if ($review->reviewed_by != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update your own reviews'
            ], 403);
        }

        // Update review
        $review->update($request->only(['rating', 'comment']));

        // Update user ratings
        $this->updateUserRatings($review->driver_id, $review->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'review' => $review->load(['passenger:id,name', 'driver:id,name'])
        ]);
    }

    
    public function deleteReview($id)
    {
        $user = Auth::user();
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user is the reviewer
        if ($review->reviewed_by != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own reviews'
            ], 403);
        }

        $driver_id = $review->driver_id;
        $user_id = $review->user_id;

        // Delete review
        $review->delete();

        // Update user ratings
        $this->updateUserRatings($driver_id, $user_id);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    public function getReviewStats()
    {
        $user = Auth::user();

        $driverStats = Review::where('driver_id', $user->id)
            ->where('type', 'driver')
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as total_reviews')
            ->first();

        $passengerStats = Review::where('user_id', $user->id)
            ->where('type', 'passenger')
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as total_reviews')
            ->first();

        return response()->json([
            'success' => true,
            'stats' => [
                'as_driver' => [
                    'average_rating' => round($driverStats->average_rating ?? 0, 1),
                    'total_reviews' => $driverStats->total_reviews ?? 0,
                    'rating_breakdown' => $this->getRatingBreakdown($user->id, 'driver')
                ],
                'as_passenger' => [
                    'average_rating' => round($passengerStats->average_rating ?? 0, 1),
                    'total_reviews' => $passengerStats->total_reviews ?? 0,
                    'rating_breakdown' => $this->getRatingBreakdown($user->id, 'passenger')
                ]
            ]
        ]);
    }

    
    private function getRatingBreakdown($userId, $type)
    {
        $query = $type == 'driver' 
            ? Review::where('driver_id', $userId)->where('type', 'driver')
            : Review::where('user_id', $userId)->where('type', 'passenger');

        $total = $query->count();
        
        if ($total == 0) {
            return [
                '5_star' => 0,
                '4_star' => 0,
                '3_star' => 0,
                '2_star' => 0,
                '1_star' => 0
            ];
        }

        return [
            '5_star' => $query->where('rating', 5)->count(),
            '4_star' => $query->where('rating', 4)->count(),
            '3_star' => $query->where('rating', 3)->count(),
            '2_star' => $query->where('rating', 2)->count(),
            '1_star' => $query->where('rating', 1)->count()
        ];
    }

    private function updateUserRatings($driverId, $userId)
    {
        // Update driver rating
        $driverAvgRating = Review::where('driver_id', $driverId)
            ->where('type', 'driver')
            ->avg('rating');
            
        User::where('id', $driverId)->update([
            'rating' => round($driverAvgRating ?? 0, 1)
        ]);

        // Update passenger rating
        $passengerAvgRating = Review::where('user_id', $userId)
            ->where('type', 'passenger')
            ->avg('rating');
            
        // If you have passenger_rating column in users table
        User::where('id', $userId)->update([
            'passenger_rating' => round($passengerAvgRating ?? 0, 1)
        ]);
    }
}