<?php

namespace Tests\Feature\View;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
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
    public function test_it_can_render(): void
    {
        $contents = $this->view('index', [
            'cars' => []
        ]);

        $contents->assertSee('Home Page | Car Seller');
    }
}
