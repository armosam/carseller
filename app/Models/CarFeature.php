<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarFeature extends Model
{
    use HasFactory;

    const string CAR_FEATURE_ABS = 'abs';
    const string CAR_FEATURE_AIR_CONDITIONING = 'air_conditioning';
    const string CAR_FEATURE_POWER_WINDOWS = 'power_windows';
    const string CAR_FEATURE_POWER_DOOR_LOCKS = 'power_door_locks';
    const string CAR_FEATURE_CRUISE_CONTROL = 'cruise_control';
    const string CAR_FEATURE_BLUETOOTH_CONNECTIVITY = 'bluetooth_connectivity';
    const string CAR_FEATURE_REMOTE_START = 'remote_start';
    const string CAR_FEATURE_GPS_NAVIGATION = 'gps_navigation';
    const string CAR_FEATURE_HEATED_SEATS = 'heated_seats';
    const string CAR_FEATURE_CLIMATE_CONTROL = 'climate_control';
    const string CAR_FEATURE_REAR_PARKING_SENSORS = 'rear_parking_sensors';
    const string CAR_FEATURE_LEATHER_SEATS = 'leather_seats';

    public $timestamps = false; // Disables timestamp creation

    protected $primaryKey = 'car_id';

    protected $fillable = [
        'car_id',
        'abs',
        'air_conditioning',
        'power_windows',
        'power_door_locks',
        'cruise_control',
        'bluetooth_connectivity',
        'remote_start',
        'gps_navigation',
        'heated_seats',
        'climate_control',
        'rear_parking_sensors',
        'leather_seats',
        'created_at',
        'updated_at',
    ];

    public static function featuresList(): array
    {
        return [
            self::CAR_FEATURE_ABS => 'ABS',
            self::CAR_FEATURE_AIR_CONDITIONING => 'Air Conditioning',
            self::CAR_FEATURE_POWER_WINDOWS => 'Power Windows',
            self::CAR_FEATURE_POWER_DOOR_LOCKS => 'Power Door Locks',
            self::CAR_FEATURE_CRUISE_CONTROL => 'Cruise Control',
            self::CAR_FEATURE_BLUETOOTH_CONNECTIVITY => 'Bluetooth Connectivity',
            self::CAR_FEATURE_REMOTE_START => 'Remote Start',
            self::CAR_FEATURE_GPS_NAVIGATION => 'GpsNavigation',
            self::CAR_FEATURE_HEATED_SEATS => 'Heated Seats',
            self::CAR_FEATURE_CLIMATE_CONTROL => 'Climate Control',
            self::CAR_FEATURE_REAR_PARKING_SENSORS => 'Rear Parking Sensors',
            self::CAR_FEATURE_LEATHER_SEATS => 'Leather Seats'
        ];
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }
}
