<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConsultancyRequest;
use App\Models\EmergencyRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Services\OpsAssistantService;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(OpsAssistantService $opsAssistantService)
    {
        $stats = [
            'users' => User::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'payments' => Payment::count(),
            'services' => ServiceRequest::count(),
            'consultancies' => ConsultancyRequest::count(),
            'bookings' => Booking::count(),
            'emergencies' => EmergencyRequest::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'open_emergencies' => EmergencyRequest::whereNotIn('status', ['resolved', 'closed'])->count(),
            'total_paid' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $recentOrders = Order::with('user')->latest()->take(8)->get();
        $recentPayments = Payment::with('user')->latest()->take(8)->get();
        $recentEmergencies = EmergencyRequest::latest()->take(8)->get();
        $trackedOrders = Order::with('user', 'items.trackingEvents')->latest()->take(6)->get();
        $trackedServices = ServiceRequest::with('user', 'trackingEvents', 'assignedStaff')->latest()->take(6)->get();
        $assistantInsights = $opsAssistantService->buildDashboardInsights($trackedOrders, $trackedServices);

        $months = collect(range(5, 0))->reverse()
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset));

        $analytics = [
            'labels' => $months->map(fn (Carbon $month) => $month->format('M Y'))->values(),
            'orders' => $months->map(fn (Carbon $month) => Order::whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count())->values(),
            'revenue' => $months->map(fn (Carbon $month) => round((float) Payment::where('status', Payment::STATUS_PAID)->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->sum('amount'), 2))->values(),
            'payment_status' => [
                'labels' => ['Paid', 'Pending', 'Under Review', 'Failed', 'Cancelled'],
                'data' => [
                    Payment::where('status', Payment::STATUS_PAID)->count(),
                    Payment::where('status', Payment::STATUS_PENDING)->count(),
                    Payment::where('status', Payment::STATUS_UNDER_REVIEW)->count(),
                    Payment::where('status', Payment::STATUS_FAILED)->count(),
                    Payment::where('status', Payment::STATUS_CANCELLED)->count(),
                ],
            ],
            'module_activity' => [
                'labels' => ['Orders', 'Services', 'Consultancy', 'Bookings', 'Emergency'],
                'data' => [
                    $stats['orders'],
                    $stats['services'],
                    $stats['consultancies'],
                    $stats['bookings'],
                    $stats['emergencies'],
                ],
            ],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'analytics',
            'assistantInsights',
            'recentOrders',
            'recentPayments',
            'recentEmergencies'
        ));
    }
}
