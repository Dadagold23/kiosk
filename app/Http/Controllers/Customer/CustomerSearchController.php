<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConsultancyRequest;
use App\Models\EmergencyRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerSearchController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $userId = (int) auth()->id();
        $results = [];
        $totalMatches = 0;

        if ($search !== '') {
            $results = [
                'orders' => Order::with('payments')
                    ->where('user_id', $userId)
                    ->where(function ($query) use ($search) {
                        $query->where('order_no', 'like', "%{$search}%")
                            ->orWhere('order_status', 'like', "%{$search}%")
                            ->orWhere('payment_status', 'like', "%{$search}%")
                            ->orWhereHas('payments', function ($paymentQuery) use ($search) {
                                $paymentQuery->where('reference', 'like', "%{$search}%")
                                    ->orWhere('receipt_no', 'like', "%{$search}%");
                            })
                            ->orWhereHas('items', function ($itemQuery) use ($search) {
                                $itemQuery->where('item_name', 'like', "%{$search}%")
                                    ->orWhereHas('product', function ($productQuery) use ($search) {
                                        $productQuery->where('name', 'like', "%{$search}%");
                                    });
                            });
                    })
                    ->latest()
                    ->take(5)
                    ->get(),
                'services' => ServiceRequest::with('category')
                    ->where('user_id', $userId)
                    ->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get(),
                'consultancies' => ConsultancyRequest::with('category')
                    ->where('user_id', $userId)
                    ->where(function ($query) use ($search) {
                        $query->where('subject', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get(),
                'bookings' => Booking::query()
                    ->where('user_id', $userId)
                    ->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhere('booking_type', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhere('payment_status', 'like', "%{$search}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get(),
                'emergencies' => EmergencyRequest::query()
                    ->where('user_id', $userId)
                    ->where(function ($query) use ($search) {
                        $query->where('full_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('location_text', 'like', "%{$search}%")
                            ->orWhere('emergency_type', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get(),
                'wishlist' => WishlistItem::with('product.category')
                    ->where('user_id', $userId)
                    ->whereHas('product', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->latest()
                    ->take(5)
                    ->get(),
                'products' => Product::with('category')
                    ->where('status', true)
                    ->where('name', 'like', "%{$search}%")
                    ->latest()
                    ->take(5)
                    ->get(),
            ];

            $totalMatches = collect($results)->sum(fn ($items) => $items->count());
        }

        return view('customer.search.index', compact('search', 'results', 'totalMatches'));
    }
}
