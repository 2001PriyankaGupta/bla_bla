<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\RideController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Api\SupportController;

use App\Http\Controllers\Api\PaymentController;

use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\NotificationController;

Route::get('test', function() {
    return response()->json(['message' => 'API is working']);
});

Route::post('ping', function() {
    return response()->json(['message' => 'pong']);
});

// Public API routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('/verify-code', [AdminAuthController::class, 'verifyResetCode']);
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword']);
    Route::post('/google-login', [SocialAuthController::class, 'googleLogin']);
});

// Public ride search routes
Route::post('/search-ride', [RideController::class, 'searchRides']);
@Route::get('/flexible-search', [RideController::class, 'flexibleSearch']);
Route::get('/{id}/seats', [RideController::class, 'getRideSeats']);

// Public trip details
Route::get('/trip/{id}', [RideController::class, 'getTripDetails']);
Route::post('/trip/{id}/share', [RideController::class, 'shareRide']);

// Public contact driver
Route::post('/trip/{id}/contact', [RideController::class, 'contactDriver']);

// PUBLIC REVIEWS APIs (No authentication required)
Route::get('/driver/{id}/reviews', [ReviewController::class, 'getDriverReviews']);
Route::get('/user/{id}/reviews', [ReviewController::class, 'getUserReviews']);

// Support & FAQ routes (Public)
Route::get('/faqs', [SupportController::class, 'getFaqs']);

 

// Protected API routes (JWT + Admin check)
Route::middleware(['api_auth'])->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::post('/refresh', [AdminAuthController::class, 'refresh']);
        Route::get('/me', [AdminAuthController::class, 'me']);
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::post('/update', [ProfileController::class, 'updateProfile']);
        Route::get('/get', [ProfileController::class, 'getProfile']);

    });

    // Cars routes
    Route::get('/cars', [CarController::class, 'index']);
    Route::post('/cars', [CarController::class, 'store']);
    Route::get('/cars/{id}', [CarController::class, 'show']);
    Route::post('/cars/{id}', [CarController::class, 'update']);
    Route::delete('/cars/{id}', [CarController::class, 'destroy']);
    Route::get('cars/{id}/verification', [CarController::class, 'checkVerification']);

    // Rides routes
    Route::get('rides', [RideController::class, 'index']);
    Route::get('rides/{id}', [RideController::class, 'show']);
    Route::post('rides', [RideController::class, 'store']);
    Route::post('rides/{id}', [RideController::class, 'update']);
    Route::delete('rides/{id}', [RideController::class, 'destroy']);

    Route::get('active-rides', [RideController::class, 'active_rides']);

    // Booking routes (# Passenger Only)
    Route::post('/trip/{id}/book', [BookingController::class, 'bookRide']);
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancelBooking']);
    # Driver Only
    Route::post('/booking/{id}/status', [BookingController::class, 'updateBookingStatus']);
    Route::get('/my-bookings', [BookingController::class, 'getUserBookings']);
    
    // Payment specific routes
    Route::post('/payment/verify', [PaymentController::class, 'verifyPayment']);
    
    // RIDE CONFIRM SCREEN APIs
    Route::get('/booking/{id}/confirm-details', [BookingController::class, 'getBookingConfirmation']);
    Route::get('/ride/{id}/driver-details', [RideController::class, 'getDriverDetails']);
    Route::get('/driver/{id}/eta', [RideController::class, 'getDriverETA']);
    
    // Driver Contact APIs
    Route::post('/driver/{id}/call', [MessageController::class, 'initiateCall']);
    Route::post('/driver/{id}/masked-number', [MessageController::class, 'getMaskedNumber']);
    
    // Message routes
    Route::post('/ride/{id}/message', [MessageController::class, 'sendMessage']);
    Route::get('/ride/{id}/conversation/{userId}', [MessageController::class, 'getConversation']);
    
    // REVIEWS SYSTEM APIs (Authentication required)
    Route::prefix('reviews')->group(function () {
      Route::get('/my-driver-reviews', [ReviewController::class, 'getMyDriverReviews']);
      Route::get('/my-passenger-reviews', [ReviewController::class, 'getMyPassengerReviews']);
        Route::post('/submit', [ReviewController::class, 'submitReview']);
        Route::put('/{id}/update', [ReviewController::class, 'updateReview']);
        Route::delete('/{id}/delete', [ReviewController::class, 'deleteReview']);
        Route::get('/stats', [ReviewController::class, 'getReviewStats']);
    });

    // Support & FAQ routes
    Route::get('/tickets', [SupportController::class, 'getTickets']);
    Route::post('/tickets', [SupportController::class, 'createTicket']);
    Route::get('/tickets/{id}', [SupportController::class, 'getTicketDetails']);
    Route::post('/tickets/{id}/reply', [SupportController::class, 'replyTicket']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});