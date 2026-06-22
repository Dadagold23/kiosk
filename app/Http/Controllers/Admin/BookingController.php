<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Notifications\RequestAssignedNotification;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('user');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }



    public function show(Booking $booking)
    {
        $booking->load('user', 'payments', 'reviews.user', 'reviews.moderator');

        return view('admin.bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'payment_status' => ['required', 'string', 'max:50'],
            'confirmation_code' => ['nullable', 'string', 'max:255'],
        ]);

        $booking->update($validated);

        if ($booking->user) {
            $booking->user->notify(new RequestAssignedNotification(
                'booking',
                ucfirst($booking->booking_type) . ' Booking',
                route('customer.bookings.show', $booking)
            ));
        }


        return back()->with('success', 'Booking updated successfully.');
    }
}
