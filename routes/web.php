<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ShopController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\ConsultancyController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\EmergencyController;
use App\Http\Controllers\Frontend\InfoPageController;
use App\Http\Controllers\Frontend\NewsletterSubscriptionController;
use App\Http\Controllers\Frontend\SitePreferenceController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ServiceRequestController;
use App\Http\Controllers\Customer\ConsultancyRequestController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminServiceRequestController;
use App\Http\Controllers\Admin\ConsultancyRequestController as AdminConsultancyRequestController;
use App\Http\Controllers\Customer\UserBookingController;
use App\Http\Controllers\Customer\UserEmergencyController;
use App\Http\Controllers\Customer\ModuleReviewController as CustomerModuleReviewController;
use App\Http\Controllers\Customer\ReceiptController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\CustomerSearchController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Payments\PaystackController;
use App\Http\Controllers\ProfileController as AccountProfileController;



Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/payments/paystack/callback', [PaystackController::class, 'callback'])->name('payments.paystack.callback');
Route::post('/webhooks/paystack', [PaystackController::class, 'webhook'])->name('payments.paystack.webhook');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/consultancy', [ConsultancyController::class, 'index'])->name('consultancy.index');
Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::get('/emergency', [EmergencyController::class, 'index'])->name('emergency.index');
Route::post('/emergency/store', [EmergencyController::class, 'store'])->name('emergency.store');
Route::get('/about-kiosk', [InfoPageController::class, 'about'])->name('info.about');
Route::get('/kiosk-branches', [InfoPageController::class, 'branches'])->name('info.branches');
Route::get('/contact-kiosk', [InfoPageController::class, 'contact'])->name('info.contact');
Route::get('/shipping-and-services', [InfoPageController::class, 'shippingServices'])->name('info.shipping');
Route::get('/returns-and-advisory', [InfoPageController::class, 'returnsAdvisory'])->name('info.returns');
Route::get('/privacy-and-booking', [InfoPageController::class, 'privacyBooking'])->name('info.privacy');
Route::get('/orders-and-faqs', [InfoPageController::class, 'ordersFaqs'])->name('info.faqs');
Route::get('/profile/geo-options', [ProfileController::class, 'geoOptions'])->name('profile.geo-options');
Route::post('/profile/detect-country', [ProfileController::class, 'detectCountry'])->name('profile.detect-country');
Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])->name('newsletter.subscribe');
Route::post('/site/preferences', [SitePreferenceController::class, 'store'])->middleware('auth')->name('site.preferences.store');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:order_no}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/order/success/{order:order_no}', [OrderController::class, 'success'])->name('orders.success');
    Route::post('/orders/{order:order_no}/pay', [OrderController::class, 'pay'])->name('orders.pay');

    Route::get('/my-services', [ServiceRequestController::class, 'index'])->name('customer.services.index');
    Route::get('/my-services/create', [ServiceRequestController::class, 'create'])->name('customer.services.create');
    Route::post('/my-services', [ServiceRequestController::class, 'store'])->name('customer.services.store');
    Route::post('/my-services/{serviceRequest}/pay', [ServiceRequestController::class, 'pay'])->name('customer.services.pay');
    Route::get('/my-services/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('customer.services.show');

    Route::get('/my-consultancy', [ConsultancyRequestController::class, 'index'])->name('customer.consultancy.index');
    Route::get('/my-consultancy/create', [ConsultancyRequestController::class, 'create'])->name('customer.consultancy.create');
    Route::post('/my-consultancy', [ConsultancyRequestController::class, 'store'])->name('customer.consultancy.store');
    Route::post('/my-consultancy/{consultancyRequest}/pay', [ConsultancyRequestController::class, 'pay'])->name('customer.consultancy.pay');
    Route::get('/my-consultancy/{consultancyRequest}', [ConsultancyRequestController::class, 'show'])->name('customer.consultancy.show');

    Route::get('/my-bookings', [UserBookingController::class, 'index'])->name('customer.bookings.index');
    Route::get('/my-bookings/create', [UserBookingController::class, 'create'])->name('customer.bookings.create');
    Route::post('/my-bookings', [UserBookingController::class, 'store'])->name('customer.bookings.store');
    Route::post('/my-bookings/{booking}/pay', [UserBookingController::class, 'pay'])->name('customer.bookings.pay');
    Route::get('/my-bookings/{booking}', [UserBookingController::class, 'show'])->name('customer.bookings.show');

    Route::get('/my-emergency', [UserEmergencyController::class, 'index'])->name('customer.emergency.index');
    Route::get('/my-emergency/{emergencyRequest}', [UserEmergencyController::class, 'show'])->name('customer.emergency.show');
    Route::get('/my-emergency/{emergencyRequest}/tracking', [UserEmergencyController::class, 'tracking'])->name('customer.emergency.tracking');
    
    Route::get('/receipts/{payment}', [ReceiptController::class, 'show'])->name('receipts.show');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/search', [CustomerSearchController::class, 'index'])->name('customer.search');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['post', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [AccountProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/receipts/{payment}/download', [ReceiptController::class, 'download'])->name('receipts.download');
    Route::post('/reviews/{type}/{record}', [CustomerModuleReviewController::class, 'store'])->name('reviews.store');

});

   

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
