<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FavoriteCar extends Model
{
    use HasFactory;

    public $timestamps = false; // Disables timestamp creation

    protected $fillable = ['user_id', 'car_id'];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'id', 'car_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
