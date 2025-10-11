<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WatchlistControllerTest extends TestCase
{
    // Will migrate database for each test
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_not_authenticated_access_redirection_to_login_page(): void
    {
        $response = $this->get('/watchlist/');
        $response->assertStatus(302);
        $response->assertRedirect('/auth/login');
        $response->assertDontSee('My Favourite Cars');
    }

    public function test_authenticated_access_response_status(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('watchlist.index'));
        $response->assertStatus(200);
        $response->assertSee('Favorite Cars | ' . config('app.name'));
        $response->assertSee('<h1 class="page-title">My Favourite Cars</h1>', false);
        $response->assertSee('Logout');
        $response->assertSee('You do not have any favourite cars.');
    }
}
