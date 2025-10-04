<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // This is with none eager loading implementation
        /*$cars = Car::query()
            ->where('published_at', '<', now())
            ->orderByDesc('published_at')
            ->limit(30)
            ->get();*/

        //dd(exec('whoami'));

        // Eager loading implementation
        /*$cars = Car::query()
            ->with(['maker', 'model', 'primaryImage', 'city.state', 'carType', 'fuelType', 'favouredUsers'])
            ->where('published_at', '<', now())
            ->orderByDesc('published_at')
            ->limit(30)
            ->get();*/

        $cache_key = 'home-cars-'.request()->get('page', 1);

        // Caching data  per page with duration of 60 sec
        $cars = Cache::remember($cache_key, 60, function () {
            return Car::query()
                ->with(['maker', 'model', 'primaryImage', 'city.state', 'carType', 'fuelType', 'favouredUsers'])
                ->where('published_at', '<', now())
                ->orderByDesc('published_at')
                ->limit(30)
                ->get();
        });


        return view('index', ['cars' => $cars]);
    }

    public function mondaySale(): View {
        return view('monday-sale');
    }
}
