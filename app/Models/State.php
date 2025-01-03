<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    public $timestamps = false; // Disables timestamp creation

    protected $fillable = ['name'];


    public function cars(): HasMany {
        return $this->hasMany(Car::class, 'state_id', 'id');
    }

    public function cities(): HasMany {
        return $this->hasMany(City::class, 'state_id', 'id');
    }
}
