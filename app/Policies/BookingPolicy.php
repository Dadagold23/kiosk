<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Booking Manager']);
    }

    public function view(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id || $user->hasAnyRole(['Super Admin', 'Admin', 'Booking Manager']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Booking Manager']);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
};
