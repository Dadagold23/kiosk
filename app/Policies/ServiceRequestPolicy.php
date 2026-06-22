<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Service Manager']);
    }

    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        return $serviceRequest->user_id === $user->id
            || $serviceRequest->assigned_to === $user->id
            || $user->hasAnyRole(['Super Admin', 'Admin', 'Service Manager']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Service Manager']);
    }

    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
};
