<?php

namespace App\Policies;

use App\Models\Motorcycle;
use App\Models\User;

class MotorcyclePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Motorcycle $motorcycle): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('manager');
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $user->hasRole('manager');
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->hasRole('manager');
    }

    public function restore(User $user, Motorcycle $motorcycle): bool
    {
        return $user->hasRole('manager');
    }

    public function forceDelete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->hasRole('manager');
    }
}
