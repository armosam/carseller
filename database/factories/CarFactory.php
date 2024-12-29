<?php

namespace Database\Factories;

use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'maker_id' => Maker::query()->inRandomOrder()->first()->id,
            'model_id' => function(array $attributes) {
                return Model::query()->where('maker_id', $attributes['maker_id'])->inRandomOrder()->firstOrFail()->id;
            },
            'year' => fake()->year(),
            'price' => (int)fake()->randomFloat(2, 5, 100) * 1000,
            'vin' => strtoupper(Str::random(17)),
            'millage' => (int)fake()->randomFloat(2, 5, 500) * 1000,
            'interior_color' => fake()->colorName(),
            'exterior_color' => fake()->colorName(),
            'car_type_id' => CarType::query()->inRandomOrder()->first()->id,
            'fuel_type_id' => FuelType::query()->inRandomOrder()->first()->id,
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'city_id' => City::query()->inRandomOrder()->first()->id,
            'address' => fake()->address(),
            'phone' => function (array $attributes) {
                return User::query()->find($attributes['user_id'])->phone;
            },
            'description' => fake()->text(2000),
            'published_at' => fake()->dateTimeBetween('-1 month', '+1 day')->format('Y-m-d H:i:s'),
            'deleted_at' => null,
        ];
    }
}
