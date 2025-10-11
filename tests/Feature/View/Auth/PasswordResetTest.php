<?php

namespace Tests\Feature\View\Auth;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    public function test_password_reset_page_can_render_with_errors(): void
    {
        $errorBag = new \Illuminate\Support\ViewErrorBag();
        $errorBag->put('default', new \Illuminate\Support\MessageBag([
            'email' => ['The email is required.'],
            'token' => ['The token is required.'],
        ]));

        // Faking Errors and injecting into view
        View::share('errors', $errorBag);

        $contents = $this->view('auth.password-reset', [
            'email' => '',
            'token' => '',
        ]);

        $contents->assertSee('Password Reset | ' . config('app.name')); // title tag
        $contents->assertSee('Password Reset'); // heading
        $contents->assertSee('value=""', false); // token field
        $contents->assertSee('name="email"', false); // email input
        $contents->assertSee('name="password"', false); // email input
        $contents->assertSee('name="password_confirmation"', false); // email input
        $contents->assertSee('The email is required.'); // validation error
        $contents->assertSee('The token is required.'); // validation error

        View::share('errors', null);
    }
}
