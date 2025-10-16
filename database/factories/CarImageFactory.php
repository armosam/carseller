<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarImage>
 */
class CarImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'image_path' => fake()->imageUrl(), // Is broken, Using custom placeholder site
            'image_path' => function (array $attributes) {
                $car = Car::find($attributes['car_id']);
                $colors = [1 => 'lightblue', 2 => 'lightgreen', 3 => 'orange', 4 => 'lightyellow', 5 => 'lightgray'];

                return sprintf('https://placehold.co/600x400/%s/png?font=oswald&text=%s-%s', $colors[$attributes['position']], $car->maker->name, $attributes['position']);
            },
            'position' => function (array $attributes) {
                return Car::query()->find($attributes['car_id'])->images()->count() + 1;
            },
        ];
    }
}
