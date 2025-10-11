<?php

namespace Tests\Feature\View;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    // Will migrate database for each test
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_response_status(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * A basic view test example.
     */
    public function test_it_can_render_with_no_cars(): void
    {
        // Empty Collection
        $cars = \Illuminate\Support\Collection::make();
        $contents = $this->view('index', [
            'cars' => $cars
        ]);

        $contents->assertSee('Home Page | ' . config('app.name'));
        $contents->assertSee('Login');
        $contents->assertSee('Signup');
        $contents->assertSee('<h2>Latest Added Cars</h2>', false);
        $contents->assertSee('There are no cars to show');
    }

    public function test_it_can_render_with_cars(): void
    {
        // Mock Car Model
        $car = new \App\Models\Car();
        $car->id = 1;
        $car->year = 2025;
        $car->price = 10000;
        $car->mileage = 5000;
        $car->published_at = now()->subDay();
        $car->exists = true;

        // Simulate relationships
        $car->setRelation('maker', new \App\Models\Maker(['name' => 'Toyota']));
        $car->setRelation('model', new \App\Models\Model(['name' => 'Corolla']));
        $car->setRelation('primaryImage', null); // or mock if needed
        $car->setRelation('city', new \App\Models\City(['name' => 'Little Elm']));
        $car->city->setRelation('state', new \App\Models\State(['name' => 'Texas']));
        $car->setRelation('carType', new \App\Models\CarType(['name' => 'SUV']));
        $car->setRelation('fuelType', new \App\Models\FuelType(['name' => 'Petrol']));
        $car->setRelation('favouredUsers', collect());

        $cars = \Illuminate\Support\Collection::make([$car]);
        $contents = $this->view('index', [
            'cars' => $cars
        ]);

        $contents->assertSee('Home Page | ' . config('app.name'));
        $contents->assertSee('Login');
        $contents->assertSee('Signup');
        $contents->assertSee('<h2>Latest Added Cars</h2>', false);
        $contents->assertDontSee('There are no cars to show');
        $contents->assertViewHas('cars', function ($cars) use ($car) {
            return $cars->contains($car) && $cars->count() === 1;
        });
        $contents->assertSee('/car/1');
        $contents->assertSee('2025 - Toyota Corolla');
        $contents->assertSee('$10000');
        $contents->assertSee('5000m');
        $contents->assertSee('Petrol');
        $contents->assertSee('Little Elm, Texas');
    }
}
