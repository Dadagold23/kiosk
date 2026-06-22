<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ModuleReviewService;

class BookingController extends Controller
{
    public function index(ModuleReviewService $moduleReviewService)
    {
        $types = [
            'hotel' => 'Hotel Booking',
            'resort' => 'Resort Booking',
            'lounge' => 'Lounge Reservation',
            'park' => 'Park Booking',
            'flight' => 'Flight Booking',
        ];

        $testimonials = $moduleReviewService->testimonialsFor('booking');

        return view('frontend.booking.index', compact('types', 'testimonials'));
    }
}
