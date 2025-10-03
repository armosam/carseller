<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class CarPolicy
{
    /**
     * Perform pre-authorization checks.
     * It will run before any ability method and
     * if returns true then accepts that ability
     * if returns false then denys that ability
     * if returns null then execution goes to that ability's method
     * Could be used to give full access (by returning true) it's an admin user
     * $arg is a model that should be changed, but for creation it will be class name of that model
     * For example:
     * if ability is creat for car then $arg will be a string App/Model/Car
     * if ability is update for car then $arg will be the model of car
     */
    public function before(?User $user, string $ability, $arg): bool|null
    {
        if ($user?->can('administrator')) {
            return true;
        }

        return null;
    }

    /**
     * Same functionality is here as before.
     * It will override result of any ability's method after if returned tru or false
     * If returned null then will take a result of last ability's method.
     * For Example
     * if ability was 'create' and policy method returned true, but after method did check and returned false
     * then final result will be false.
     * If after returns null then final result will be true.
     */
    public function after(?User $user, Car $car): bool|null
    {
        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Car $car): Response
    {
        // The owner can see not published car but others not
        return (!empty($car->published_at) || $car->owner()->is($user))
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        // return ($user->is(Auth::user()) && !empty($user->phone));
        // With custom message
        return ($user->is(Auth::user()) && !empty($user->phone))
             ? Response::allow()
             : Response::deny('You are not authorized to create a car. Please make sure to enter your profile phone number');
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
