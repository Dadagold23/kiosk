<?php

namespace App\Policies;

use App\Models\ConsultancyRequest;
use App\Models\User;

class ConsultancyRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Consultant Manager']);
    }

    public function view(User $user, ConsultancyRequest $consultancyRequest): bool
    {
        return $consultancyRequest->user_id === $user->id
            || $consultancyRequest->assigned_consultant_id === $user->id
            || $user->hasAnyRole(['Super Admin', 'Admin', 'Consultant Manager']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ConsultancyRequest $consultancyRequest): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Consultant Manager']);
    }

    public function delete(User $user, ConsultancyRequest $consultancyRequest): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
};
