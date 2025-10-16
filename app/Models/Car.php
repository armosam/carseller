<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Car extends EloquentModel
{
    use HasFactory;

    // When calling delete on model then instead of removing record, it will update record and set deleted_at column.
    use SoftDeletes;

    protected $fillable = [
        'maker_id',
        'model_id',
        'year',
        'price',
        'vin',
        'mileage',
        'interior_color',
        'exterior_color',
        'car_type_id',
        'fuel_type_id',
        'user_id',
        'state_id',
        'city_id',
        'address',
        'phone',
        'description',
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = []; // not allowing to fill data if provided

    /**
     * In case of create, update or delete model it will forget cached data
     * It will iterate to forget cache for paginated data
     *
     * @return void
     */
    protected static function booted()
    {
        $forget_cache = function () {
            $count = self::query()->count();
            for ($i = 1; $i <= $count / 30; $i++) {
                Cache::forget('home-cars-'.$i);
            }
            for ($i = 1; $i <= $count / 15; $i++) {
                Cache::forget('favorite-cars-'.$i);
            }
            for ($i = 1; $i <= $count / 5; $i++) {
                Cache::forget('my-cars-'.$i);
            }
        };

        static::created(function ($car) use ($forget_cache) {
            $forget_cache();
        });
        static::updated(function ($car) use ($forget_cache) {
            $forget_cache();
        });
        static::deleted(function ($car) use ($forget_cache) {
            $forget_cache();
        });
    }

    public function features(): HasOne
    {
        return $this->hasOne(CarFeature::class, 'car_id', 'id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(CarImage::class, 'car_id', 'id')->oldestOfMany('position');
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class, 'car_id', 'id')->orderBy('position');
    }

    public function carType(): BelongsTo
    {
        return $this->belongsTo(CarType::class, 'car_type_id', 'id');
    }

    public function favouredUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_cars', 'car_id', 'user_id');
        // ->withTimestamps();
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Maker::class, 'maker_id', 'id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class, 'model_id', 'id');
    }

    public function getFormattedDate($column_name, $format = 'Y-m-d'): string
    {
        if (isset($this->{$column_name})) {
            return Carbon::parse($this->{$column_name})->format($format);
        }

        return (new Carbon)->format($format);
    }

    public function getTitle(): string
    {
        return $this->year.' - '.$this->maker->name.' '.$this->model->name;
    }
}
