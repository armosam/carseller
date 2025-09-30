<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'google_id',
        'facebook_id',
        'password',
        'email_verified_at',
    ];

    //protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Returns full name of user */
    public function fullName(): string {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    /**
     * Returns true if user is signed by socialite
     * Otherwise false
     * @return bool
     */
    public function isOauthUser(): bool
    {
        return !$this->password;
    }

    /**
     * User's added cars
     * @return HasMany
     */
    public function cars(): HasMany {
        return $this->hasMany(Car::class, 'user_id', 'id');
    }

    /**
     * User's favorite cars connected through pivot table
     * @return BelongsToMany
     */
    public function favoriteCars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'favorite_cars', 'user_id', 'car_id')
            ->withPivot('id')
            ->orderBy('favorite_cars.id', 'desc'); // To order watchlist by descending ID
            //->withTimestamps();
    }
}
