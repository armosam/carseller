<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelType extends Model
{
    use HasFactory;

    // protected $table = 'fuel_types_customized';
    // protected $primaryKey = 'id_customized';
    // public $incrementing = false; // Disables auto incrementing the ID
    // protected $keyType = 'string'; // Changes primary key to string
    // const CREATED_AT = 'createdAt'; //Customize created_at to your own
    // const UPDATED_AT = null; // Disable updated_at

    public $timestamps = false; // Disables timestamp creation

    protected $fillable = ['name'];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'fuel_type_id', 'id');
    }
}
