<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
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
        $cars = Car::query()
            ->with(['maker', 'model', 'primaryImage', 'city.state', 'carType', 'fuelType'])
            ->where('published_at', '<', now())
            ->orderByDesc('published_at')
            ->limit(30)
            ->get();


        return view('index', ['cars' => $cars]);
    }
}
