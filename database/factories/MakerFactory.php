<?php

namespace Database\Factories;

use App\Models\Maker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Maker>
 */
class MakerFactory extends Factory
{
    // protected $model = Maker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }

    /**
     * Custom method
     * @return Factory
     */
    public function toyota(): Factory {
        return $this->state(function (array $attributes) {
            return ['name' => 'Toyota'];
        });
    }

    /** Custom method
     * @param string $name
     * @return Factory
     */
    public function makerSequence(string $name): Factory {
        return $this->state( new Sequence( fn(Sequence $sequence) => ['name' => "$name $sequence->index"] ) );
    }
}
