<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarFeature;
use App\Models\CarImage;
use App\Models\CarType;
use App\Models\User;
use App\Mail\CarAdded;
use App\Rules\PhoneRule;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use App\Http\Requests\Car\StoreCarRequest;
use App\Jobs\TranslateJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
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


        /*// Associate a new car type to the car
        $car = Car::find(1)->first();
        $carTypeHatchback = CarType::where('name', 'Hatchback')->first();
        $car->carType()->associate($carTypeHatchback);
        $car->save();

        // Manage many-to-many pivot table
        $user = User::find(1);
        // Adds many to many connection in the pivot table
        $user->favoriteCars->attache([1,2], ['column1' => 'value1']);

        // Removes all connections from pivot table and adds new connections
        $user->favoriteCars->sync([1,2]);
        $user->favoriteCars->syncWithPivotValues([1,2], ['column1' => 'value1']);

        // Removes given connections and if not provided then removes all
        $user->favoriteCars->detach([1,2]);
        $user->favoriteCars->detach();*/


        /*// Sessions
        $request = request();
        $request->session()->pull('user', 'Laravel User');
        session(['user' => 'Laravel Simple User']);

        // $user = $request->session()->get('user', 'Default User');
        $user = session('user', 'Default User');
        $all = $request->session()->all();

        $request->session()->forget('user');
        $user = $request->session()->remove('user');

        dump($all, $user);*/

        /*// Keep all flash message sessions
        request()->session()->reflash();
        // Keep only success message session
        request()->session()->keep(['success']);*/


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
    public function create(): View
    {
        return view('car.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request)
    {
        $images = $request->file('images') ?: [];
        $data = $request->all();
        $featureAttributes = $data['features'] ?? [];
        $attributes = $request->validated();

        /*// Simply use rules to validate each field
        $attributes = $request->validated([
            'maker_id' => 'required',
            'model_id' => 'required',
            'year' => ['required', 'integer', 'min:2000', 'max:'.date('Y')],
            //'phone' => 'required|string|min:10'
            //'phone' => new PhoneRule(),
            'phone' => function (string $attribute, mixed $value, Closure $fail) {
                if (!is_numeric($value) || strlen($value) !== 11) {
                    $fail("The :attribute must be 11 characters.");
                    //$fail("The {$attribute} must be 10 characters.");
                }
            },
            'features' => 'array',
            'features.*' => 'string',
            'images' => 'array',
            //'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096', // Simple way or
            'images.*' => File::image()
                ->min(10)
                ->max(4096)
                ->dimensions(Rule::dimensions()->maxWidth(1024)->maxHeight(768))
        ], [ // Custom validation messages
            'required' => 'Please enter the :attribute.',
            'maker_id.required' => 'Please enter the maker.',
        ], [ // attribute names
            'maker_id' => 'Make Name',
            'model_id' => 'Model Name'
        ]);

        // Create custom and specific validator
        $validator = Validator::make($request->all(), [
            'maker_id' => 'required',
            'model_id' => 'required',
            'year' => ['required', 'integer', 'min:2000', 'max:'.date('Y')],
        ], [
            'required' => 'Please enter the :attribute.',
            'maker_id.required' => 'Please enter the maker.',
        ], [
            'maker_id' => 'Make Name',
            'model_id' => 'Model Name'
        ]);
        // or
        $validator = Validator::make($request->all(), [
            'maker_id' => Rule::requiredIf(fn() => $request->year == 2020)
        ]);

        if($validator->fails()) {
            // Redirect, log ... etc. validation errors
            return redirect(route('car.create'))
                ->withErrors($validator)
                ->withInput();
        }

        // Access to the validated data
        $attributes = $validator->validated();
        $attributes = $request->safe()->only(['maker_id', 'year', 'vin']);
        $attributes = $request->safe()->except(['year', 'vin']);
        $attributes = $request->safe()->merge(['user_id', Auth->id()]);
        */

        $newCar = Car::query()->create($attributes);
        $newCar->features()->create($featureAttributes);

        foreach ($images as $position => $image) {
            if($image->isFile() && $image->isReadable()) {
                $path = $image->store('images');
                $newCar->images()->create(['image_path' => $path, 'position' => $position + 1]);
            }
        }

        // Sending email about car created
        //Mail::to(Auth::user())->send(new CarAdded($newCar));

        // Adding queue job to send an email
        Mail::to(Auth::user())->queue(new CarAdded($newCar));

        return to_route('car.show', ['car' => $newCar])->with('success', 'Car created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Car $car): View
    {
        if (empty($car->published_at)) {
            abort(404);
        }

        // get session message
        // $message = $request->session()->get('success');

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
    public function update(StoreCarRequest $request, Car $car)
    {
        $attributes = $request->validated();
        $featuresAttributes = $attributes['features'] ?? [];

        $car->update($attributes);

        // Because checkboxes returning only checked values as '1'
        // we need to collect a feature list with 0 values and
        // merge it with received from form features
        // Get all feature names
        $allFeatures = array_keys(CarFeature::featuresList());
        // Reset values of all features to 0
        $allFeatures = array_fill_keys($allFeatures, 0);
        // Merge received features to feature list with 0 values
        $featuresAttributes = array_merge($allFeatures, $featuresAttributes);

        $car->features()->update($featuresAttributes);

        // Set flash message
        //$request->session()->flash('success', 'Car was updated');

        return to_route('car.show', ['car' => $car])->with('success', 'Car updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        $car->delete();

        return to_route('car.index')->with('success', 'Car deleted.');
    }

    public function carImages (Car $car): View
    {
        return view('car.images', ['car' => $car]);
    }

    public function addImages(Request $request, Car $car)
    {
        $request->validate([
            'images' => 'array',
            //'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096', // Simple way or
            'images.*' => File::image()
                ->extensions('jpeg,png,jpg,gif,svg')
                ->max(config('image.max_size'))
                //->dimensions(Rule::dimensions()->maxWidth(config('image.max_width'))->maxHeight(config('image.max_height')))
        ]);

        $images = $request->file('images') ?? [];
        $position = $car->images()->max('position') ?? 0;

        foreach ($images as $image) {
            $car->images()->create(['image_path' => $image->store('images'), 'position' => ++$position]);
        }

        return to_route('car.images', ['car' => $car])->with('success', 'Images added.');
    }

    public function updateImages (Request $request, Car $car)
    {
        $data = $request->validate([
            'delete_images' => 'array',
            'delete_images.*' => 'integer|exists:car_images,id',
            'positions' => 'array',
            'positions.*' => 'integer',
        ]);

        $deleteImages = $data['delete_images'] ?? [];
        $positions = $data['positions'] ?? [];

        // Update positions of Images
        foreach ($positions as $imageId => $position) {
            $car->images()->where('id', $imageId)->update(['position' => $position]);
        }

        // Delete images from file storage
        $imagesToDelete = $car->images()->whereIn('id', $deleteImages)->get();
        foreach ($imagesToDelete as $image) {
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
            }
        }
        // Delete images from database
        $car->images()->whereIn('id', $deleteImages)->delete();

        return to_route('car.images', ['car' => $car])->with('success', 'Images updated.');
    }

    /**
     * Search record
     */
    public function search(Request $request): View
    {
        $maker = $request->integer('maker_id');
        $model = $request->integer('model_id');
        $state = $request->integer('state_id');
        $city = $request->integer('city_id');
        $car_type = $request->integer('car_type_id');
        $fuel_type = $request->integer('fuel_type_id');
        $year_from = $request->integer('year_from');
        $year_to = $request->integer('year_to');
        $price_from = $request->float('price_from');
        $price_to = $request->float('price_to');
        $mileage = $request->integer('mileage');

        $sort = $request->input('sort', '-published_at');

        $query = Car::query()
            ->with(['maker', 'model', 'primaryImage', 'city' => ['state'], 'carType', 'fuelType'])
            ->where('published_at', '<', now());


        if ($maker) {
            $query->where('maker_id', $maker);
        }
        if ($model) {
            $query->where('model_id', $model);
        }
        if ($state) {
            $query->join('cities', 'cities.id', '=', 'cars.city_id')
                ->where('cities.state_id', $state);
        }
        if ($city) {
            $query->where('city_id', $city);
        }
        if ($car_type) {
            $query->where('car_type_id', $car_type);
        }
        if ($fuel_type) {
            $query->where('fuel_type_id', $fuel_type);
        }
        if ($year_from) {
            $query->where('year', '>=', $year_from);
        }
        if ($year_to) {
            $query->where('year', '<=', $year_to);
        }
        if ($price_from) {
            $query->where('price', '>=', $price_from);
        }
        if ($price_to) {
            $query->where('price', '<=', $price_to);
        }
        if ($mileage) {
            $query->where('mileage', '<=', $mileage);
        }

        if (str_starts_with($sort, '-')) {
            $sort = substr($sort, 1);
            $query->orderBy($sort, 'desc');
        } else {
            $query->orderBy($sort);
        }

        /*
        $query->reorder() // Removes ordering
              ->orderBy('price', 'desc');  //Adds a new ordering
        // Removes ordering and adds new one
        $query->reorder('price');
        */


        /*// REQUEST
        dump($request->all());
        dump($request->only(['price_from', 'price_to']));
        dump($request->except(['price_from', 'price_to']));
        dump($request->get('price_from', 0.00));
        dump($request->post('price_from', 0.00));
        dump($request->input('year_from', 0.00));
        dump($request->query('maker_id', 111));
        dump($request->has('maker_id'));
        dump($request->filled('maker_id'));
        dump($request->integer('maker_id'));
        dump($request->float('price_from'));
        dump($request->boolean('published'));
        dump($request->date('published_at'));
        dump($request->file('image'));

        dump($request->path());
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


        $query->dump();
        $query->dd();
        $query->toSql();
        $query->ddRawSql();*/

        $cars = $query->paginate(15)
            ->withQueryString();

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
