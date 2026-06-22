<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConsultancyRequest;
use App\Models\EmergencyRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Services\PaystackService;

class DashboardController extends Controller
{
    public function index(PaystackService $paystackService)
    {
        $user = auth()->user();

        if ($user->hasBackOfficeHome()) {
            return redirect()->to($user->homePath());
        }

        $userId = $user->id;

        $stats = [
            'orders' => Order::where('user_id', $userId)->count(),
            'services' => ServiceRequest::where('user_id', $userId)->count(),
            'consultancy' => ConsultancyRequest::where('user_id', $userId)->count(),
            'bookings' => Booking::where('user_id', $userId)->count(),
            'emergencies' => EmergencyRequest::where('user_id', $userId)->count(),
            'payments' => Payment::where('user_id', $userId)->count(),
            'wishlist' => $user->wishlistItems()->count(),
            'notifications' => $user->notifications()->count(),
            'pending_orders' => Order::where('user_id', $userId)->whereIn('order_status', ['pending', 'reviewing', 'processing', 'sourced', 'dispatched'])->count(),
            'pending_payments' => Payment::where('user_id', $userId)->whereIn('status', ['pending', 'under_review'])->count(),
            'delivered_orders' => Order::where('user_id', $userId)->where('order_status', Order::STATUS_DELIVERED)->count(),
            'paid_total' => Payment::where('user_id', $userId)->where('status', Payment::STATUS_PAID)->sum('amount'),
        ];

        $recentOrders = Order::with(['payments', 'items'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with('payable')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $recentServices = ServiceRequest::where('user_id', $userId)
            ->latest()
            ->take(4)
            ->get();

        $recentConsultancy = ConsultancyRequest::where('user_id', $userId)
            ->latest()
            ->take(4)
            ->get();

        $recentBookings = Booking::where('user_id', $userId)
            ->latest()
            ->take(4)
            ->get();

        $recentEmergencies = EmergencyRequest::where('user_id', $userId)
            ->latest()
            ->take(4)
            ->get();

        $recentWishlistItems = $user->wishlistItems()
            ->with('product.category')
            ->latest()
            ->take(3)
            ->get();

        $recentNotifications = $user->notifications()
            ->latest()
            ->take(3)
            ->get();

        $profileChecks = [
            'email' => filled($user->email) && $paystackService->acceptsCustomerEmail($user->email),
            'phone' => filled($user->phone),
            'address' => filled($user->address),
        ];

        $profileCompletion = (int) round((collect($profileChecks)->filter()->count() / count($profileChecks)) * 100);
        $nextStep = match (true) {
            ! $profileChecks['email'] => 'Update your email to a real public address so Paystack payments can initialize without interruption.',
            ! $profileChecks['phone'] => 'Add your phone number so dispatch and emergency teams can reach you quickly.',
            ! $profileChecks['address'] => 'Save your delivery address to speed up checkout and fulfillment.',
            $stats['pending_payments'] > 0 => 'You have pending payments waiting for confirmation or completion.',
            $stats['pending_orders'] > 0 => 'You have active orders currently in processing or delivery.',
            default => 'Your account is ready for shopping, bookings, and service requests.',
        };

        return view('customer.dashboard', compact(
            'stats',
            'profileChecks',
            'profileCompletion',
            'nextStep',
            'recentOrders',
            'recentPayments',
            'recentServices',
            'recentConsultancy',
            'recentBookings',
            'recentEmergencies',
            'recentWishlistItems',
            'recentNotifications'
        ));
    }
}
