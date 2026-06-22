<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConsultancyRequestController as AdminConsultancyRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmergencyController as AdminEmergencyController;
use App\Http\Controllers\Admin\MarketplaceController;
use App\Http\Controllers\Admin\ModuleReviewController as AdminModuleReviewController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminServiceRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DemoController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->middleware('role:Super Admin|Admin')
            ->name('dashboard');

        Route::get('/search', [AdminSearchController::class, 'index'])
            ->middleware('role:Super Admin|Admin|Shop Manager|Service Manager|Consultant Manager|Booking Manager|Emergency Desk')
            ->name('search');

        Route::get('/demo/{page}.html', [DemoController::class, 'show'])
            ->middleware('role:Super Admin|Admin|Shop Manager|Service Manager|Consultant Manager|Booking Manager|Emergency Desk')
            ->name('demo');

        Route::post('/reviews/{moduleReview}/moderate', [AdminModuleReviewController::class, 'moderate'])
            ->middleware('role:Super Admin|Admin|Shop Manager|Service Manager|Consultant Manager|Booking Manager|Emergency Desk')
            ->name('reviews.moderate');

        Route::middleware('role:Super Admin|Admin|Shop Manager|Service Manager|Consultant Manager|Booking Manager|Emergency Desk')->group(function () {
            Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        });

        Route::middleware('role:Super Admin|Admin')->group(function () {
            Route::resource('/categories', CategoryController::class)->except(['show']);
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/verify-kyc', [UserController::class, 'verifyKyc'])->name('users.verify-kyc');
        });

        Route::middleware('role:Super Admin|Admin|Shop Manager')->group(function () {
            Route::resource('/products', ProductController::class);
            Route::get('/marketplaces', [MarketplaceController::class, 'index'])->name('marketplaces.index');
            Route::post('/marketplaces/sync', [MarketplaceController::class, 'sync'])->name('marketplaces.sync');
            Route::post('/marketplaces/providers/{provider}/toggle', [MarketplaceController::class, 'toggleProvider'])->name('marketplaces.providers.toggle');
            Route::post('/marketplaces/products/{product}/status', [MarketplaceController::class, 'updateProductStatus'])->name('marketplaces.products.status');
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{order}/update', [AdminOrderController::class, 'update'])->name('orders.update');
            Route::post('/orders/{order}/items/{item}/tracking', [AdminOrderController::class, 'updateItemTracking'])->name('orders.items.update');

            Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
            Route::post('/payments/{payment}/update', [PaymentController::class, 'update'])->name('payments.update');
        });

        Route::middleware('role:Super Admin|Admin|Service Manager')->group(function () {
            Route::get('/service-requests', [AdminServiceRequestController::class, 'index'])->name('services.index');
            Route::get('/service-requests/{serviceRequest}', [AdminServiceRequestController::class, 'show'])->name('services.show');
            Route::post('/service-requests/{serviceRequest}/assign', [AdminServiceRequestController::class, 'assign'])->name('services.assign');
            Route::post('/service-requests/{serviceRequest}/tracking', [AdminServiceRequestController::class, 'track'])->name('services.track');
        });

        Route::middleware('role:Super Admin|Admin|Consultant Manager')->group(function () {
            Route::get('/consultancy-requests', [AdminConsultancyRequestController::class, 'index'])->name('consultancy.index');
            Route::get('/consultancy-requests/{consultancyRequest}', [AdminConsultancyRequestController::class, 'show'])->name('consultancy.show');
            Route::post('/consultancy-requests/{consultancyRequest}/assign', [AdminConsultancyRequestController::class, 'assign'])->name('consultancy.assign');
        });

        Route::middleware('role:Super Admin|Admin|Booking Manager')->group(function () {
            Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
            Route::post('/bookings/{booking}/update', [AdminBookingController::class, 'update'])->name('bookings.update');
        });

        Route::middleware('role:Super Admin|Admin|Emergency Desk')->group(function () {
            Route::get('/emergency-requests', [AdminEmergencyController::class, 'index'])->name('emergency.index');
            Route::get('/emergency-requests/{emergencyRequest}', [AdminEmergencyController::class, 'show'])->name('emergency.show');
            Route::post('/emergency-requests/{emergencyRequest}/update', [AdminEmergencyController::class, 'update'])->name('emergency.update');
            Route::post('/emergency-requests/{emergencyRequest}/tracking', [AdminEmergencyController::class, 'track'])->name('emergency.track');
        });
    });
