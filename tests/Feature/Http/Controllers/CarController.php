<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CarController extends TestCase
{
    // Will migrate database for each test
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_not_authenticated_access_redirection_to_login_page(): void
    {
        $this->get('/car/')
            ->assertStatus(302)
            ->assertRedirect('/auth/login')
            ->assertDontSee('My Cars');
    }

    public function test_authenticated_access_response_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/car/')
            ->assertStatus(200)
            ->assertSee('My Cars | ' . config('app.name'))
            ->assertSee('<h1 class="page-title">My Cars</h1>', false)
            ->assertSee('Add New Car')
            ->assertSee(['Image', 'Title', 'Date', 'Published', 'Actions'])
            ->assertSee('You do not have any cars yet.');
    }

    public function test_search_page_response_status(): void
    {
        $this->get('/car/search')
            ->assertStatus(200)
            ->assertSee('Search Criteria');
    }

    public function test_car_creation_with_empty_data(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('car.store'), [
            'id' => null,
            'maker_id' => null,
            'model_id' => null,
            'year' => null,
            'price' => null,
            'vin' => null,
            'mileage' => null,
            'interior_color' => null,
            'exterior_color' => null,
            'car_type_id' => null,
            'fuel_type_id' => null,
            'state_id' => null,
            'city_id' => null,
            'address' => null,
            'phone' => null,
            'description' => null,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['id', 'user_id', 'description', 'published_at', 'created_at', 'updated_at', 'deleted_at',])
            ->assertSessionHasErrors([
                'maker_id',
                'model_id',
                'year',
                'price',
                'vin',
                'mileage',
                'interior_color',
                'exterior_color',
                'car_type_id',
                'fuel_type_id',
                'state_id',
                'city_id',
                'address',
                'phone',
            ]);
    }

    public function test_car_creation_with_invalid_data(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('car.store'), [
            'maker_id' => 1000,
            'model_id' => 1000,
            'year' => 'Invalid',
            'price' => 'invalid',
            'vin' => '111',
            'mileage' => 'invalid',
            'interior_color' => 1000,
            'exterior_color' => 1000,
            'car_type_id' => 1000,
            'fuel_type_id' => 1000,
            'state_id' => 1000,
            'city_id' => 1000,
            'address' => 1000,
            'phone' => 123,
            'description' => null,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['id', 'user_id', 'description', 'published_at', 'created_at', 'updated_at', 'deleted_at'])
            ->assertInvalid([
                'maker_id' => ['The selected Maker is invalid.'],
                'model_id' => ['The selected Model is invalid.'],
                'year' => ['The Year field must be a number.', 'The Year field must be between 1990 and 2025.'],
                'vin' => ['The VIN Number field must be at least 17 characters.'],
                'price' => ['The Price field must be a number.'],
                'mileage' => ['The Mileage field must be an integer.'],
                'interior_color' => ['The Interior Color field must be a string.'],
                'exterior_color' => ['The Exterior Color field must be a string.'],
                'car_type_id' => ['The selected Car Type is invalid.'],
                'fuel_type_id' => ['The selected Fuel Type is invalid.'],
                'state_id' => ['The selected State is invalid.'],
                'city_id' => ['The selected City is invalid.'],
                'address' => ['The Address field must be a string.'],
                'phone' => ['The Phone must be 11 numeric characters.'],
            ]);
    }

    public function test_car_creation_successfully_with_valid_data(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $carCount = Car::count();
        $imageCount = CarImage::count();

        // Create 5 fake images to import
        $images = [
            UploadedFile::fake()->image('image_1.jpg'),
            UploadedFile::fake()->image('image_2.jpg'),
            UploadedFile::fake()->image('image_3.jpg'),
            UploadedFile::fake()->image('image_4.jpg'),
            UploadedFile::fake()->image('image_5.jpg'),
        ];

        $response = $this->actingAs($user)->post(route('car.store'), [
            'maker_id' => 1,
            'model_id' => 2,
            'year' => 2025,
            'price' => 15000,
            'vin' => '123456789abCt123a',
            'mileage' => 20000,
            'interior_color' => 'Black',
            'exterior_color' => 'White',
            'car_type_id' => 1,
            'fuel_type_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Test Address',
            'phone' => 18889991234,
            'description' => null,
            'features' => [
                'air_conditioning' => '1',
                'power_windows' => '1',
                'power_door_locks' => '1',
                'abs' => '1',
            ],
            'images' => $images
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('car.show', ['car' => $carCount + 1])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Car Created Successfully.');

        $this->assertDatabaseCount('cars', $carCount + 1);
        $this->assertDatabaseHas('cars', [
            'user_id' => $user->id,
            'maker_id' => 1,
            'model_id' => 2,
            'year' => 2025,
            'price' => 15000,
            'vin' => '123456789ABCT123A',
            'mileage' => 20000,
            'interior_color' => 'Black',
            'exterior_color' => 'White',
            'car_type_id' => 1,
            'fuel_type_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Test Address',
            'phone' => '18889991234',
            'description' => null,
        ]);

        $this->assertDatabaseCount('car_images', $imageCount + count($images));
        $this->assertDatabaseHas('car_images', ['car_id' => $carCount + 1]);

        $this->assertDatabaseCount('car_features',  $carCount + 1);
        $this->assertDatabaseHas('car_features', [
            'car_id' => $carCount + 1,
            'air_conditioning' => 1,
            'power_windows' => 1,
            'power_door_locks' => 1,
            'abs' => 1,
            'cruise_control' => 0,
            'bluetooth_connectivity' => 0,
            'remote_start' => 0,
            'gps_navigation' => 0,
            'heated_seats' => 0,
            'climate_control' => 0,
            'rear_parking_sensors' => 0,
            'leather_seats' => 0,
            ]);
    }

    public function test_car_update_page_renders_with_correct_data(): void
    {
        $this->seed();
        $user = User::query()->first();
        $car = $user->cars()->first();

        $response = $this->actingAs($user)->get(route('car.edit', ['car' => $car]));

        $response->assertStatus(200)
            ->assertViewIs('car.edit')
            ->assertViewHas('car', $car)
            ->assertSeeInOrder([
                '<select id="makerSelect" name="maker_id"',
                '<option selected value="' . $car->maker_id . '"',
                $car->maker->name,
                '</option'
            ], false)
            ->assertSeeInOrder([
                '<select id="modelSelect" name="model_id"',
                '<option selected value="' . $car->model_id . '"',
                $car->model->name,
                '</option'
            ], false)
            ->assertSeeInOrder([
                '<select name="year"',
                '<option selected value="' . $car->year . '"',
                $car->year,
                '</option'
            ], false)
            ->assertSeeInOrder([
                '<input type="radio" name="car_type_id" value="'.$car->car_type_id.'"',
                'checked',
                $car->carType->name,
            ], false)
            ->assertSeeInOrder([
                'input type="number" name="price" value="'.$car->price.'" placeholder="Price"',
            ], false)
            ->assertSeeInOrder([
                'input type="text" name="vin" value="'.$car->vin.'" placeholder="VIN Number"',
            ], false)
            ->assertSeeInOrder([
                'input type="number" name="mileage" value="'.$car->mileage.'" placeholder="Mileage"',
            ], false)
            ->assertSeeInOrder([
                '<input type="radio" name="fuel_type_id" value="'.$car->fuel_type_id.'"',
                'checked',
                $car->fuelType->name,
            ], false)
            ->assertSeeInOrder([
                'input type="text" name="interior_color" value="'.$car->interior_color.'" placeholder="Interior Color"',
            ], false)
            ->assertSeeInOrder([
                'input type="text" name="exterior_color" value="'.$car->exterior_color.'" placeholder="Exterior Color"',
            ], false)
            ->assertSeeInOrder([
                '<select id="stateSelect" name="state_id"',
                '<option selected value="' . $car->state_id . '"',
                $car->state->name,
                '</option'
            ], false)
            ->assertSeeInOrder([
                '<select id="citySelect" name="city_id"',
                '<option selected value="' . $car->city_id . '"',
                $car->city->name,
                '</option'
            ], false)
            ->assertSeeInOrder([
                'input type="text" name="address" value="'.$car->address.'" placeholder="Address"',
            ], false)
            ->assertSeeInOrder([
                'input type="tel" name="phone" value="'.$car->phone.'" placeholder="Phone"',
            ], false)
            ->assertSeeInOrder([
                'textarea name="description" rows="5"',
                $car->description . '</textarea'
            ], false)
            ->assertSeeInOrder([
                'input type="date" name="published_at" value="'.date_create($car->published_at)->format('Y-m-d').'"',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[abs]" value="1"',
                $car->features['abs'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[air_conditioning]" value="1"',
                $car->features['air_conditioning'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[power_windows]" value="1"',
                $car->features['power_windows'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[power_door_locks]" value="1"',
                $car->features['power_door_locks'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[cruise_control]" value="1"',
                $car->features['cruise_control'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[bluetooth_connectivity]" value="1"',
                $car->features['bluetooth_connectivity'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[remote_start]" value="1"',
                $car->features['remote_start'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[gps_navigation]" value="1"',
                $car->features['gps_navigation'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[heated_seats]" value="1"',
                $car->features['heated_seats'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[climate_control]" value="1"',
                $car->features['climate_control'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[rear_parking_sensors]" value="1"',
                $car->features['rear_parking_sensors'] == 1 ? 'checked' : '',
            ], false)
            ->assertSeeInOrder([
                'input type="checkbox" name="features[leather_seats]" value="1"',
                $car->features['leather_seats'] == 1 ? 'checked' : '',
            ], false);

    }

    public function test_car_update_successfully_with_valid_data(): void
    {
        $this->seed();
        $user = User::query()->first();
        $car = $user->cars()->first();

        $response = $this->actingAs($user)->put(route('car.update', $car), [
            'maker_id' => 1,
            'model_id' => 2,
            'year' => 2025,
            'price' => 15000,
            'vin' => '123456789abCt123a',
            'mileage' => 20000,
            'interior_color' => 'Black',
            'exterior_color' => 'White',
            'car_type_id' => 1,
            'fuel_type_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Test Address',
            'phone' => 18889991234,
            'description' => null,
            'features' => [
                'air_conditioning' => '1',
                'power_windows' => '1',
                'power_door_locks' => '1',
                'abs' => '1',
                /*'cruise_control' => '0',
                'bluetooth_connectivity' => '0',
                'remote_start' => '0',
                'gps_navigation' => '0',
                'heated_seats' => '0',
                'climate_control' => '0',*/
                'rear_parking_sensors' => '1',
                'leather_seats' => '1',
            ],
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('car.show', ['car' => $car])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Car Updated Successfully.');

        $this->assertDatabaseHas('cars', [
                'user_id' => $user->id,
                'maker_id' => 1,
                'model_id' => 2,
                'year' => 2025,
                'price' => 15000,
                'vin' => '123456789ABCT123A',
                'mileage' => 20000,
                'interior_color' => 'Black',
                'exterior_color' => 'White',
                'car_type_id' => 1,
                'fuel_type_id' => 1,
                'state_id' => 1,
                'city_id' => 1,
                'address' => 'Test Address',
                'phone' => '18889991234',
                'description' => null,
            ])
            ->assertDatabaseHas('car_features', [
                'car_id' => $car->id,
                'air_conditioning' => 1,
                'power_windows' => 1,
                'power_door_locks' => 1,
                'abs' => 1,
                'cruise_control' => 0,
                'bluetooth_connectivity' => 0,
                'remote_start' => 0,
                'gps_navigation' => 0,
                'heated_seats' => 0,
                'climate_control' => 0,
                'rear_parking_sensors' => 1,
                'leather_seats' => 1,
            ]);
    }

    public function test_car_delete_successfully(): void
    {
        $this->seed();

        $carCount = Car::query()->count();
        $user = User::query()->first();
        $car = $user->cars()->first();

        $response = $this->actingAs($user)->delete(route('car.destroy', $car));

        $response->assertStatus(302)
            ->assertRedirectToRoute('car.index')
            ->assertSessionHas('success', 'Car Deleted Successfully.');

        $this->assertDatabaseCount('cars', $carCount);
        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'deleted_at' => now(),
        ]);
    }

    public function test_add_new_images_to_car(): void
    {
        $this->seed();
        $user = User::query()->first();
        $car = $user->cars()->first();

        $oldImageCount = $car->images()->count();

        // Create 5 fake images to add to the car
        $images = [
            UploadedFile::fake()->image('image_1.jpg'),
            UploadedFile::fake()->image('image_2.jpg'),
            UploadedFile::fake()->image('image_3.jpg'),
            UploadedFile::fake()->image('image_4.jpg'),
            UploadedFile::fake()->image('image_5.jpg'),
        ];

        $response = $this->actingAs($user)->post(route('car.addImages', $car), [
            'images' => $images,
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('car.images', ['car' => $car])
            ->assertSessionHas('success', 'Images Added Successfully.');

        $this->assertDatabaseHas('car_images', ['car_id' => $car->id]);
        $this->assertEquals($oldImageCount + count($images), $car->images()->count());
    }

    public function test_delete_images_from_car(): void
    {
        $this->seed();
        $user = User::query()->first();
        $car = $user->cars()->first();

        $oldImageCount = $car->images()->count();
        $deletedImageIds = $car->images()->pluck('id')->toArray();

        $response = $this->actingAs($user)->put(route('car.updateImages', $car), [
            'delete_images' => $deletedImageIds
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('car.images', ['car' => $car])
            ->assertSessionHas('success', 'Images Updated Successfully.');

        $this->assertEquals($oldImageCount - count($deletedImageIds), $car->images()->count());
    }

    public function test_update_positions_of_images_for_car(): void
    {
        $this->seed();
        $user = User::query()->first();
        // Order user cars by descending ID
        $car = $user->cars()->reorder('id', 'desc')->first();

        // Order car images by descending positions and return an array
        $carImages = $car->images()->reorder('position', 'desc')->get()->toArray();
        // Get a new array of ID => Position
        $newImagePositions = Arr::pluck($carImages, 'position', 'id');

        $response = $this->actingAs($user)->put(route('car.updateImages', $car), [
            'positions' => $newImagePositions
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('car.images', ['car' => $car])
            ->assertSessionHas('success', 'Images Updated Successfully.');

        foreach ($carImages as $carImage) {
            $this->assertDatabaseHas('car_images', [
                'car_id' => $car->id,
                'id' => $carImage['id'],
                'position' => $carImage['position'],
            ]);
        }
    }

    public function test_user_cannot_edit_other_user_cars(): void
    {
        $this->seed();
        [$user1, $user2] = User::query()->limit(2)->get();
        $user1Car = $user1->cars()->first();

        $response = $this->actingAs($user2)->get(route('car.edit', $user1Car));

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_other_user_cars(): void
    {
        $this->seed();
        [$user1, $user2] = User::query()->limit(2)->get();
        $user1Car = $user1->cars()->first();

        $response = $this->actingAs($user2)->post(route('car.destroy', $user1Car));

        $response->assertStatus(405);
    }

    public function test_authenticated_user_can_view_other_user_cars(): void
    {
        $this->seed();
        [$user1, $user2] = User::query()->limit(2)->get();
        $user1Car = $user1->cars()->first();

        $response = $this->actingAs($user2)->get(route('car.show', $user1Car));

        $response->assertStatus(200)
            ->assertViewIs('car.show')
            ->assertSee($user1Car->getTitle())
            ->assertSee($user1Car->year)
            ->assertSee($user1Car->vin)
            ->assertSee($user1Car->mileage)
            ->assertSee($user1Car->maker->name)
            ->assertSee($user1Car->model->name)
            ->assertSee($user1Car->carType->name)
            ->assertSee($user1Car->fuelType->name)
            ->assertSee($user1Car->address)
            ->assertSee($user1Car->owner->fullName())
            ->assertSee($user1Car->description)
            ->assertSee($user1Car->images[0]->image_path);

        foreach ($user1Car->images as $image) {
            $response->assertSee($image->image_path)
                ->assertSee('img src="' . htmlentities($image->image_path) . '"', false);
        }
    }

    public function test_not_authenticated_user_cannot_view_car_detail(): void
    {
        $this->seed();

        $user = User::query()->first();
        $car = $user->cars()->first();

        $response = $this->get(route('car.show', $car));

        $response->assertStatus(302)
            ->assertRedirectToRoute('login');
    }

}


