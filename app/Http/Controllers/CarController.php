<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\CarType;
use App\Models\User;
use App\Mail\CarAdded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use App\Http\Requests\Car\StoreRequest;
use App\Jobs\TranslateJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*return response('Hello there', 404);

        return response()->json(['message' => 'Hello there'], 404)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET');

        return response()->view('car.show',  ['car' => Car::query()->first()], 404)
            ->header('Content-Type', 'application/json');

        return redirect('car/create');
        return redirect()->route('car.show', ['car' => 1]);
        return redirect()->route('car.show', Car::first());
        return redirect()->away('https://littlebeeline.com', 301);*/


        // It gets first authenticated user, then returns cars of that user
        $cars = User::query()->find(Auth::id())
            ->cars()
            ->with(['maker', 'model', 'primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
//            ->withPath('user/cara');
//            ->appends(['some-sort' => 'price'])
//            ->withQueryString()
//            ->fragment('cars');
        return view('car.index', ['cars' => $cars]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('car.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $attributes = $request->validated();
        //$attributes = $request->safe()->only(['maker_id', 'year', 'vin']);

        $newCar = Car::query()->create($attributes);

        // Sending email about car created
        //Mail::to(Auth::user())->send(new CarAdded($newCar));

        // Adding queue job to send an email
        Mail::to(Auth::user())->queue(new CarAdded($newCar));

        return to_route('car.show', ['car' => $newCar]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Car $car): View
    {
        /*dump($request->path());
        dump($request->url());
        dump($request->fullUrl());
        dump($request->method());
        dump($request->isMethod('post'));
        dump($request->isXmlHttpRequest());
        dump($request->is('car/*'));
        dump($request->routeIs('car.*'));
        dump($request->expectsJson());
        dump($request->fullUrlWithQuery(['sort' => 'price']));

        dump($request->fullUrlWithoutQuery(['sort']));
        dump($request->host());
        dump($request->httpHost());
        dump($request->schemeAndHttpHost());
        dump($request->header());
        dump($request->bearerToken());
        dump($request->ip());*/

        return view('car.show', ['car' => $car]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car): View
    {
        /*Gate::define('car_update', function (User $user, Car $car) {
            return $car->owner()->is($user);
        });

        if ($car->owner()->isNot(auth()->user())) {
            abort(403, 'You are not authorized to modify this car.');
        }*/

        /*if (Auth::user()->cannot('car_update', $car)) {
            abort(403);
        }*/

        // Gate::authorize('car_update', $car);


        // Adds a job to the queue in 10 sec using queue closure
        /*dispatch(function () {
            logger('Hello World');
        })->delay(10);*/


        // Using dedicated job class to add a job to the queue
        TranslateJob::dispatch($car);

        return view('car.edit', ['car' => $car]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, Car $car)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        //
    }

    /**
     * Search record
     */
    public function search(): View
    {
        $query = Car::query()
            ->with(['maker', 'model', 'primaryImage', 'city' => ['state'], 'carType', 'fuelType'])
            ->where('published_at', '<', now())
            ->orderBy('published_at', 'desc')
            ->orderBy('price', 'desc');
            //or
//        $query = Car::query()
//            ->with(['maker', 'model', 'primaryImage', 'city' => ['state'], 'carType', 'fuelType'])
//            ->where('published_at', '<', now())
//            ->latest('published_at')
//            ->oldest('price');

        $query->reorder() // Removes ordering
              ->orderBy('price', 'desc');  //Adds a new ordering

        $query->reorder('price'); // Removes ordering and adds new one

        /*// JOINS
        $query->join('cities', 'cars.city_id', '=', 'cities.id')
            ->where('cities.state_id', '=', 15);
        // If you are joining to some table for some reason then it make sense to add columns in the select rather than doing eager loading
        $query->select('cars.*', 'cities.name as cityName');

        $query->join('car_images', function (JoinClause $join) {
            $join->on('cars.id', '=', 'car_images.car_id')
                //->orOn('some_other_condition_here')
                ->where('car_images.position', '=', 1);
        });

        $query->whereNot('published_at', '<', now());

        $query->where('published_at', '>', now())
            ->orWhere('price', '>', 10000);

        $query->whereAny(['address', 'description'], 'like', '%text%');
        $query->whereAll(['address', 'description'], 'like', '%text%');

        $query->whereBetween('year', [2010, 2024]);
        $query->orWhereBetween('year', [2000, 2005]);
        $query->whereNotBetween('year', [2022, 2023]);

        $query->whereNull('year');
        $query->orWhereNull('year');
        $query->whereNotNull('year');
        $query->orWhereNotNull('year');

        $query->whereIn('year', [2010, 2024]);
        $query->orWhereIn('year', [2010, 2024]);
        $query->whereNotIn('year', [2010, 2024]);
        $query->orWhereNotIn('year', [2010, 2024]);
        $usersQuery = User::query()->select('users.id')->whereNotNull('google_id');
        $query->whereIn('user_id', $usersQuery);

        $query->whereDate('created_at', '=', '2024-11-03');
        $query->whereYear('created_at', '=', '2023');
        $query->whereMonth('created_at', '=', '11');
        $query->whereDay('created_at', '=', '02');
        $query->whereTime('created_at', '=', '10:30:00');

        $query->whereColumn('created_at', '=', 'updated_at');
        $query->whereColumn('created_at', '<', 'updated_at');
        $query->whereColumn([
            ['column1', '=', 'column2'],
            ['created_at', '<', 'updated_at']
        ]);

        $query->whereBetweenColumns('column1', ['min_allowed_value', 'max_allowed_value']);
        $query->orWhereBetweenColumns('column1', ['min_allowed_value', 'max_allowed_value']);
        $query->whereNotBetweenColumns('column1', ['min_allowed_value', 'max_allowed_value']);
        $query->orWhereNotBetweenColumns('column1', ['min_allowed_value', 'max_allowed_value']);

        $query->whereFullText('description', 'BMW');

        // Grouping AND, OR parts
        // select * from cars where price > 5000 and (year > 2010 or year < 2015);
        $query->where('price', '>', 5000)
            ->where( function(Builder $query) {
                $query->where('year', '>', '2010')
                    ->orWhere('year', '<', '2015');
            });

        // All Cars where is image exists in the car_images table
        $query->whereExists(function (\Illuminate\Database\Query\Builder $query) {
            $query->select('id')
                ->from('car_images')
                ->whereColumn('car_images.car_id', 'cars.id');
        });
        // or
        $query->whereExists(
            CarImage::query()->select('id')
            ->whereColumn('car_images.car_id', 'cars.id')
        );

        // Find Sedan Cars
        $query->where(function (\Illuminate\Database\Query\Builder $query) {
            $query->select('name')
                ->from('car_types')
                ->whereColumn('cars.car_type_id', '=', 'car_types.id')
                ->limit(1);
        }, '=', 'Sedan');
        // or
        $subquery = CarType::query()->select('car_types.name')
            ->whereColumn('car_types.id', '=', 'cars.car_type_id')
            ->limit(1);
        $query->where($subquery, '=', 'Sedan');

        // Get cars where price is below average price of all cars
        $query->where('price', '<', function(\Illuminate\Database\Query\Builder $query) {
            $query->selectRaw('AVG(price) as avg_price')->from('cars');
        });

        // Associate a new car type to the car
        $car = Car::find(1)->first();
        $carTypeHatchback = CarType::where('name', 'Hatchback')->first();
        $car->carType()->associate($carTypeHatchback);
        $car->save();

        // Manage many to many pivot table
        $user = User::find(1);
        // Adds many to many connection in the pivot table
        $user->favoriteCars->attache([1,2], ['column1' => 'value1']);

        // Removes all connections from pivot table and adds new connections
        $user->favoriteCars->sync([1,2]);
        $user->favoriteCars->syncWithPivotValues([1,2], ['column1' => 'value1']);

        // Removes given connections and if not provided then removes all
        $user->favoriteCars->detach([1,2]);
        $user->favoriteCars->detach();


        $query->dump();
        $query->dd();
        $query->toSql();
        $query->ddRawSql();*/

        $cars = $query->paginate(15);

        return view('car.search', ['cars' => $cars]);
    }

    /**
     * Display Watch list
     * @return View
     */
    public function watchlist(): View
    {
        // Find favorite cars of authenticated user
        $cars = User::query()->find(Auth::id())
            ->favoriteCars()
            ->with(['maker', 'model', 'primaryImage', 'city.state', 'carType', 'fuelType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('car.watchlist', ['cars' => $cars]);
    }
}
