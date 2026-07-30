<?php

namespace App\Policies;

use App\Models\Renter;
use App\Models\User;

class RenterPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Renter $renter): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, Renter $renter): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function delete(User $user, Renter $renter): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function restore(User $user, Renter $renter): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function forceDelete(User $user, Renter $renter): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
