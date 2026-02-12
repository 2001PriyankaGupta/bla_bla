<?php

use App\Http\Controllers\admin\auth\AuthController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\Api\RideController;
use App\Http\Controllers\admin\FarePromoController;
use App\Http\Controllers\admin\SettingController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\SupportTicketController;
use App\Http\Controllers\admin\FaqController;
use App\Http\Controllers\admin\QuickReplyTemplateController;
use App\Http\Controllers\admin\PaymentController;
use App\Http\Controllers\admin\CarController;
use App\Http\Controllers\admin\BookingController;

// Public admin auth routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login'); // CHANGED FROM '/' TO '/login'
    Route::post('/login', 'loginSubmit')->name('loginSubmit');
});

// Protected admin routes
Route::middleware(['admin'])->group(function () {
    
        
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(AdminController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        // You can also add a route for '/' if you want
        Route::get('/', 'dashboard')->name('admin');
        // Route::get('profile','profile')->name('profile');
    });

    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/update-profile', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
        Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

     
        // User Management Routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });



    // Ride Management Routes
        Route::prefix('rides')->name('rides.')->group(function () {

            Route::get('/', [RideController::class, 'rides_list'])->name('index');
        
            Route::get('/create', [RideController::class, 'create_ride'])->name('create');
            Route::post('/store', [RideController::class, 'store_ride'])->name('store');
            
            Route::get('/{ride}', [RideController::class, 'show_rides'])->name('show');
            
            Route::get('/{ride}/edit', [RideController::class, 'edit_ride'])->name('edit');
            Route::put('/{ride}/update', [RideController::class, 'update_ride'])->name('update');
            
            Route::put('/{ride}/status', [RideController::class, 'updateStatus'])->name('update-status');
            
            Route::delete('/{ride}', [RideController::class, 'destroy_rides'])->name('destroy');
        });

        // Car Management Routes
        Route::prefix('cars')->name('cars.')->group(function () {
            Route::get('/', [CarController::class, 'index'])->name('index');
            Route::get('/create', [CarController::class, 'create'])->name('create');
            Route::post('/', [CarController::class, 'store'])->name('store');
            Route::get('/{car}/edit', [CarController::class, 'edit'])->name('edit');
            Route::put('/{car}', [CarController::class, 'update'])->name('update');
            Route::delete('/{car}', [CarController::class, 'destroy'])->name('destroy');
            Route::post('/{car}/status', [CarController::class, 'updateStatus'])->name('update-status');
        });

        // Booking Management Routes
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::post('/{booking}/status', [BookingController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
        });
        Route::post('/cars/{car}/status', [CarController::class, 'updateStatus'])->name('cars.status');
        Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

        // routes/web.php



    Route::prefix('fare-promo')->group(function () {
        Route::get('/', [FarePromoController::class, 'index'])->name('fare-promo.index');
        Route::post('/save-fare', [FarePromoController::class, 'saveFareConfig'])->name('fare-promo.save-fare');
        Route::post('/save-promo', [FarePromoController::class, 'savePromoCode'])->name('fare-promo.save-promo');
        Route::get('/get-promo/{id}', [FarePromoController::class, 'getPromoCode'])->name('fare-promo.get-promo');
        Route::delete('/delete-promo/{id}', [FarePromoController::class, 'deletePromoCode'])->name('fare-promo.delete-promo');
    });

    // / Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        
        // General Settings
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('general.update');
        
        // API Keys
        Route::post('/api-keys', [SettingController::class, 'storeApiKey'])->name('api-keys.store');
        Route::put('/api-keys/{id}', [SettingController::class, 'updateApiKey'])->name('api-keys.update');
        Route::delete('/api-keys/{id}', [SettingController::class, 'deleteApiKey'])->name('api-keys.delete');
        Route::post('/api-keys/{id}/regenerate', [SettingController::class, 'regenerateApiKey'])->name('api-keys.regenerate');
        
        // Taxes
        Route::post('/taxes', [SettingController::class, 'storeTax'])->name('taxes.store');
        Route::put('/taxes/{id}', [SettingController::class, 'updateTax'])->name('taxes.update');
        Route::delete('/taxes/{id}', [SettingController::class, 'deleteTax'])->name('taxes.delete');
        
        // Roles & Permissions
        Route::post('/roles', [SettingController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{id}', [SettingController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{id}', [SettingController::class, 'deleteRole'])->name('roles.delete');
        Route::post('/assign-admin-role', [SettingController::class, 'assignAdminRole'])->name('assign-admin-role');

        // Add these routes in your admin.php routes file
        Route::get('/api-keys/{id}/edit', [SettingController::class, 'editApiKey'])->name('api-keys.edit');
        Route::get('/roles/{id}/edit', [SettingController::class, 'editRole'])->name('roles.edit');
        Route::get('/taxes/{id}/edit', [SettingController::class, 'editTax'])->name('taxes.edit');

        // Admin Users
        Route::post('/admin-users', [SettingController::class, 'storeAdminUser'])->name('admin-users.store');
        Route::put('/admin-users/{id}', [SettingController::class, 'updateAdminUser'])->name('admin-users.update');
        Route::delete('/admin-users/{id}', [SettingController::class, 'deleteAdminUser'])->name('admin-users.delete');
        Route::get('/admin-users/{id}/edit', [SettingController::class, 'editAdminUser'])->name('admin-users.edit');

        
    });

        Route::prefix('support')->group(function () {
            // Support Tickets
            Route::get('/', [SupportTicketController::class, 'index'])->name('support.index');
            Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('support.tickets.show');
            Route::post('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.tickets.status');
            Route::post('/tickets/{ticket}/assign', [SupportTicketController::class, 'assignAgent'])->name('support.tickets.assign');
            Route::post('/tickets/{ticket}/reply', [SupportTicketController::class, 'sendReply'])->name('support.tickets.reply');
            Route::delete('/tickets/{ticket}', [SupportTicketController::class, 'destroy'])->name('support.tickets.destroy');
            // In your support routes group
            Route::get('/tickets/export', [SupportTicketController::class, 'export'])->name('support.tickets.export');

            
            // FAQ Management
            Route::get('/faqs', [FaqController::class, 'index'])->name('support.faqs');
            Route::get('/faqs/create', [FaqController::class, 'create'])->name('support.faqs.create');
            Route::post('/faqs', [FaqController::class, 'store'])->name('support.faqs.store');
            Route::get('/faqs/{faq}/edit', [FaqController::class, 'edit'])->name('support.faqs.edit');
            Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('support.faqs.update');
            Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('support.faqs.destroy');
            
            // Quick Reply Templates
            Route::resource('quick-replies', QuickReplyTemplateController::class);
        });

        // payment management routes
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/export', [PaymentController::class, 'exportCsv'])->name('export');
            Route::get('/report', [PaymentController::class, 'monthlyReport'])->name('report');
        });
});