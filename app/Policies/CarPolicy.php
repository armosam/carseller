<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class CarPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Car $car): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is(Auth::user());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Car $car): bool
    {
        return $car->owner()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Car $car): bool
    {
        return $car->owner()->is($user);
    }
}
