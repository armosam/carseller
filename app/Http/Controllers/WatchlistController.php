<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        /*$cars = $user->favoriteCars()
            ->with(['maker', 'model', 'primaryImage', 'city.state', 'carType', 'fuelType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);*/

        $cache_key = 'favorite-cars-'.request()->get('page', 1);

        // Caches favorite cars per page for 60 sec. It will forget if car changed
        $cars = Cache::remember($cache_key, 60, function () use ($user) {
            return $user->favoriteCars()
                ->with(['maker', 'model', 'primaryImage', 'city.state', 'carType', 'fuelType', 'favouredUsers'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        });

        return view('watchlist.index', ['cars' => $cars]);
    }

    public function store(Car $car)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Forget paginated cache for homepage and favorite page
        $count = Car::query()->count();
        for ($i = 1; $i <= $count / 30; $i++) {
            Cache::forget('home-cars-' . $i);
        }
        for ($i = 1; $i <= $count / 15; $i++) {
            Cache::forget('favorite-cars-' . $i);
        }

        // For large data this code will not perform better
        //$carExists = $user->favoriteCars->contains($car);

        $carExists = $user->favoriteCars()->where('car_id', $car->id)->exists();

        if ($carExists) {
            $user->favoriteCars()->detach($car);
            //return back()->with('success', 'Car successfully removed from favorite list.');
            // Return JSON data
            return response()->json([
                'message' => 'Car successfully removed from favorite list.',
            ]);
        }

        $user->favoriteCars()->attach($car);
        // return back()->with('success', 'Car successfully added to favorite list.');
        return response()->json([
            'message' => 'Car successfully added to favorite list.',
        ]);
    }
}
