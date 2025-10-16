<?php

namespace Tests\Feature\View;

use Tests\TestCase;

class AboutTest extends TestCase
{
    public function test_response_status()
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
    }

    /**
     * A basic view test example.
     */
    public function test_it_can_render(): void
    {
        $contents = $this->view('about');

        $contents->assertSee('About Us | '.config('app.name'));
        $contents->assertSee('<h1>About Us</h1>', false);
    }
}
