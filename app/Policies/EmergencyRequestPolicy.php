<?php

namespace App\Policies;

use App\Models\EmergencyRequest;
use App\Models\User;

class EmergencyRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Emergency Desk']);
    }

    public function view(User $user, EmergencyRequest $emergencyRequest): bool
    {
        return $emergencyRequest->user_id === $user->id
            || $user->hasAnyRole(['Super Admin', 'Admin', 'Emergency Desk']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, EmergencyRequest $emergencyRequest): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Emergency Desk']);
    }

    public function delete(User $user, EmergencyRequest $emergencyRequest): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
};
