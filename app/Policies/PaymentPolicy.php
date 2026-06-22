<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Shop Manager']);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $payment->user_id === $user->id || $user->hasAnyRole(['Super Admin', 'Admin', 'Shop Manager']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Shop Manager']);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
};
