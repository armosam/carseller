<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Find favorite cars of authenticated user
        $cars = $user->favoriteCars()
            ->with(['maker', 'model', 'primaryImage', 'city.state', 'carType', 'fuelType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('watchlist.index', ['cars' => $cars]);
    }

    public function store(Car $car)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // For large data this code will not perform better
        //$carExists = $user->favoriteCars->contains($car);

        $carExists = $user->favoriteCars()->where('car_id', $car->id)->exists();

        if ($carExists) {
            $user->favoriteCars()->detach($car);
            return back()->with('success', 'Car successfully removed from favorite list.');
        }

        $user->favoriteCars()->attach($car);
        return back()->with('success', 'Car successfully added to favorite list.');
    }
}
