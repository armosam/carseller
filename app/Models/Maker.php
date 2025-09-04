<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maker extends EloquentModel
{
    use HasFactory;

    public $timestamps = false; // Disables timestamp creation

    protected $fillable = ['name'];

    // In case Model name and Factory name does not match
    /*protected static function newFactory()
    {
        return CustomMakerFactory::new();
    }*/


    public function cars(): HasMany {
        return $this->hasMany(Car::class, 'maker_id', 'id');
    }

    public function models(): HasMany {
        return $this->hasMany(Model::class, 'maker_id', 'id');
    }
}
