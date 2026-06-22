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
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'services' => ServiceRequest::count(),
            'consultancy' => ConsultancyRequest::count(),
            'bookings' => Booking::count(),
            'emergencies' => EmergencyRequest::count(),
            'payments' => Payment::count(),
            'paid_payments' => Payment::where('status', 'paid')->count(),
            'total_paid' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $recentPayments = Payment::with('user')->latest()->take(10)->get();
        $recentOrders = Order::with('user')->latest()->take(10)->get();
        $recentEmergencies = EmergencyRequest::latest()->take(10)->get();

        $months = collect(range(5, 0))->reverse()
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset));

        $analytics = [
            'labels' => $months->map(fn (Carbon $month) => $month->format('M Y'))->values(),
            'orders' => $months->map(fn (Carbon $month) => Order::whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count())->values(),
            'revenue' => $months->map(fn (Carbon $month) => round((float) Payment::where('status', Payment::STATUS_PAID)->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->sum('amount'), 2))->values(),
            'payments_by_status' => [
                'labels' => ['Paid', 'Pending', 'Under Review', 'Failed', 'Cancelled'],
                'data' => [
                    Payment::where('status', Payment::STATUS_PAID)->count(),
                    Payment::where('status', Payment::STATUS_PENDING)->count(),
                    Payment::where('status', Payment::STATUS_UNDER_REVIEW)->count(),
                    Payment::where('status', Payment::STATUS_FAILED)->count(),
                    Payment::where('status', Payment::STATUS_CANCELLED)->count(),
                ],
            ],
            'modules' => [
                'labels' => ['Orders', 'Services', 'Consultancy', 'Bookings', 'Emergency'],
                'data' => [
                    $stats['orders'],
                    $stats['services'],
                    $stats['consultancy'],
                    $stats['bookings'],
                    $stats['emergencies'],
                ],
            ],
        ];

        return view('admin.reports.index', compact(
            'stats',
            'analytics',
            'recentPayments',
            'recentOrders',
            'recentEmergencies'
        ));
    }
}
