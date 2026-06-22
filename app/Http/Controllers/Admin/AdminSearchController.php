<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\EmergencyRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\ConsultancyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminSearchController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sections = $this->availableSections($request->user());
        $results = [];
        $totalMatches = 0;

        if ($search !== '') {
            foreach ($sections as $section) {
                $results[$section['key']] = $this->searchSection($section['key'], $search);
            }

            $totalMatches = collect($results)->sum(fn ($items) => $items->count());
        }

        return view('admin.search.index', compact('search', 'results', 'totalMatches', 'sections'));
    }

    protected function availableSections($user): array
    {
        $sections = [];

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Shop Manager'])) {
            $sections[] = ['key' => 'orders', 'title' => 'Orders', 'empty' => 'No orders matched this search.'];
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Shop Manager'])) {
            $sections[] = ['key' => 'payments', 'title' => 'Payments', 'empty' => 'No payments matched this search.'];
            $sections[] = ['key' => 'products', 'title' => 'Products', 'empty' => 'No products matched this search.'];
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $sections[] = ['key' => 'users', 'title' => 'Users', 'empty' => 'No users matched this search.'];
            $sections[] = ['key' => 'activity_logs', 'title' => 'Activity Logs', 'empty' => 'No activity logs matched this search.'];
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Service Manager'])) {
            $sections[] = ['key' => 'services', 'title' => 'Service Requests', 'empty' => 'No service requests matched this search.'];
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Consultant Manager'])) {
            $sections[] = ['key' => 'consultancies', 'title' => 'Consultancy Requests', 'empty' => 'No consultancy requests matched this search.'];
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Booking Manager'])) {
            $sections[] = ['key' => 'bookings', 'title' => 'Bookings', 'empty' => 'No bookings matched this search.'];
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Emergency Desk'])) {
            $sections[] = ['key' => 'emergencies', 'title' => 'Emergencies', 'empty' => 'No emergencies matched this search.'];
        }

        return $sections;
    }

    protected function searchSection(string $key, string $search): Collection
    {
        return match ($key) {
            'orders' => Order::with('user')
                ->where(function ($query) use ($search) {
                    $query->where('order_no', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                })
                ->latest()
                ->take(5)
                ->get(),
            'payments' => Payment::with('user')
                ->where(function ($query) use ($search) {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhere('receipt_no', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                })
                ->latest()
                ->take(5)
                ->get(),
            'users' => User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('identity_number', 'like', "%{$search}%");
                })
                ->latest()
                ->take(5)
                ->get(),
            'products' => Product::with('category')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })
                ->latest()
                ->take(5)
                ->get(),
            'services' => ServiceRequest::with('user')
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                })
                ->latest()
                ->take(5)
                ->get(),
            'consultancies' => ConsultancyRequest::with('user')
                ->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                })
                ->latest()
                ->take(5)
                ->get(),
            'bookings' => Booking::with('user')
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                })
                ->latest()
                ->take(5)
                ->get(),
            'emergencies' => EmergencyRequest::query()
                ->where(function ($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('location_text', 'like', "%{$search}%")
                        ->orWhere('assigned_unit', 'like', "%{$search}%");
                })
                ->latest()
                ->take(5)
                ->get(),
            'activity_logs' => ActivityLog::with('user')
                ->where(function ($query) use ($search) {
                    $query->where('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                })
                ->latest()
                ->take(5)
                ->get(),
            default => collect(),
        };
    }
}
