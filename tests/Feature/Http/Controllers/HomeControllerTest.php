<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    // Will migrate database for each test
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_response_status(): void
    {
        $this->get('/')
            ->assertStatus(200);
    }

    public function test_no_cars_to_show_on_home_page(): void
    {
        $this->get('/')
            ->assertSee('There are no cars to show');
    }

    public function test_show_new_published_cars_on_home_page(): void
    {
        // This will seed whole database. So little slow
        $this->seed();

        $response = $this->get('/');
        $response
            ->assertDontSee('There are no cars to show')
            ->assertSee('<a href="'.config('app.url').'/car/', false)
            ->assertViewHas('cars', function (Collection $collection) {
                return $collection->count() == 30;
            });
    }

    public function test_not_existing_path_response_status(): void
    {
        $response = $this->get('/not-existing-path');
        $response->assertStatus(404);
        $response->assertSee('Not Found');
    }

    public function test_home_page_renders_for_guest_user(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200)
            ->assertViewIs('index')
            ->assertSeeHtmlInOrder([
                'href="'.route('login').'"',
                'Login',
            ])
            ->assertSeeHtmlInOrder([
                'href="'.route('signup').'"',
                'Signup',
            ])
            ->assertSeeHtmlInOrder([
                'href="'.route('car.search').'"',
                'Search',
            ])
            ->assertDontSee('Add New Car')
            ->assertDontSee('My Account')
            ->assertDontSee('My Favorite Cars')
            ->assertDontSee('My Cars')
            ->assertDontSee('My Profile')
            ->assertDontSee('Logout');
    }

    public function test_home_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200)
            ->assertViewIs('index')
            ->assertDontSee('Login')
            ->assertDontSee('Signup')
            ->assertSeeHtmlInOrder([
                'href="'.route('car.search').'"',
                'Search',
            ])
            ->assertSeeHtmlInOrder([
                'href="'.route('car.create').'"',
                'Add New Car',
            ])
            ->assertSeeHtmlInOrder([
                'href="javascript:void(0)"',
                'My Account',
            ])
            ->assertSeeHtmlInOrder([
                'href="'.route('watchlist.index').'"',
                'My Favourite Cars',
            ])
            ->assertSeeHtmlInOrder([
                'href="'.route('car.index').'"',
                'My Cars',
            ])
            ->assertSeeHtmlInOrder([
                'href="'.route('profile.index').'"',
                'My Profile',
            ])
            ->assertSeeHtmlInOrder([
                'button>Logout</button',
            ]);
    }
}
