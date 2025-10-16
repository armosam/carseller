<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_response_status_for_login(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertViewHas('errors', null)
            ->assertViewHas('<h1 class="auth-page-title">Login</h1>', false)
            ->assertSee('Reset Password')
            ->assertSee('Login</button>', false)
            ->assertSee('Google')
            ->assertSee('Facebook')
            ->assertSee('<input type="email" name="email"', false)
            ->assertSee('<input type="password" name="password"', false)
            ->assertSee('<button ', false)
            ->assertSee('<a href="'.route('login.oauth', 'google').'"', false)
            ->assertSee('<a href="'.route('login.oauth', 'facebook').'"', false)
            ->assertSee("Don't have an account?", false)
            ->assertSee('<a href="'.route('signup').'">Click here to create one</a>', false);
    }

    public function test_login_with_no_data(): void
    {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);
        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionHasErrors(['email', 'password'])
            ->assertInvalid([
                'email' => 'The email field is required.',
                'password' => 'The password field is required.',
            ]);
    }

    public function test_login_with_no_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'wrong_password',
        ]);
        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password'])
            ->assertInvalid(['email' => 'The email field is required.']);
    }

    public function test_login_with_no_password(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'a@test.com',
            'password' => '',
        ]);
        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['email'])
            ->assertInvalid(['password' => 'The password field is required.']);
    }

    public function test_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'a@test.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'a@test.com',
            'password' => 'wrong_password',
        ]);
        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password'])
            ->assertInvalid(['email' => 'User credentials do not match our records.']);
    }

    public function test_login_with_short_password(): void
    {
        User::factory()->create([
            'email' => 'a@test.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'a@test.com',
            'password' => '123456',
        ]);
        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionHasErrors(['password'])
            ->assertInvalid(['password']);
    }

    public function test_login_with_correct_credentials(): void
    {
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'a@test.com',
            'password' => '12345678',
        ]);
        $response->assertStatus(302)
            ->assertRedirectToRoute('car.index')
            ->assertSessionHas('success', 'Welcome back John Doe');
    }

    public function test_response_status_for_signup(): void
    {
        $response = $this->get(route('signup'));

        $response->assertStatus(200)
            ->assertViewIs('auth.signup')
            ->assertViewHas('errors', null)
            ->assertSee('<h1 class="auth-page-title">Signup</h1>', false)
            ->assertSee('Register</button>', false)
            ->assertSee('Google')
            ->assertSee('Facebook')
            ->assertSee('<input type="email" name="email"', false)
            ->assertSee('<input type="password" name="password" placeholder="Your Password"', false)
            ->assertSee('<input type="password" name="password_confirmation" placeholder="Repeat Password"', false)
            ->assertSee('<input type="text" name="first_name" placeholder="First Name', false)
            ->assertSee('<input type="text" name="last_name" placeholder="Last Name', false)
            ->assertSee('<input type="phone" name="phone" placeholder="Phone Number', false)
            ->assertSee('<button ', false)
            ->assertSee('<a href="'.route('login.oauth', 'google').'"', false)
            ->assertSee('<a href="'.route('login.oauth', 'facebook').'"', false)
            ->assertSee('Already have an account?', false)
            ->assertSee('<a href="'.route('login').'">Click here to login</a>', false);
    }

    public function test_signup_with_no_data(): void
    {
        $response = $this->post(route('signup'), [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'phone' => '',
        ]);
        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['phone'])
            ->assertInvalid(['first_name', 'last_name', 'email', 'password']);
        // ->ddSession();
    }

    public function test_signup_with_no_email(): void
    {
        $response = $this->post(route('signup'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => '',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'phone' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'phone', 'password'])
            ->assertInvalid(['email']);
    }

    public function test_signup_with_no_first_last_names(): void
    {
        $response = $this->post(route('signup'), [
            'first_name' => '',
            'last_name' => '',
            'email' => 'a@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'phone' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['email', 'phone', 'password'])
            ->assertInvalid(['first_name', 'last_name']);
    }

    public function test_signup_with_short_password(): void
    {
        $response = $this->post(route('signup'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'phone' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'email', 'phone'])
            ->assertInvalid(['password']);
    }

    public function test_signup_with_not_matching_password_confirmation(): void
    {
        $response = $this->post(route('signup'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'password' => '12345678',
            'password_confirmation' => '11111111',
            'phone' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'email', 'phone'])
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);
    }

    public function test_signup_with_wrong_phone_number(): void
    {
        $response = $this->post(route('signup'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'phone' => '111',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'email', 'password'])
            ->assertInvalid(['phone' => 'The phone field must be at least 10 characters.']);
    }

    public function test_signup_with_existing_email_address(): void
    {
        User::factory()->create([
            'first_name' => 'Baran',
            'last_name' => 'Mara',
            'email' => 'a@test.com',
            'phone' => '8889991111',
            'password' => bcrypt('12345678'),
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'first_name' => 'Baran',
            'last_name' => 'Mara',
            'email' => 'a@test.com',
            'phone' => '8889991111',
        ]);

        $response = $this->post(route('signup'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '8889991234',
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('home')
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'phone', 'password'])
            ->assertInvalid([
                'email' => 'The email has already been taken.',
            ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'phone' => '8889991234',
        ]);
    }

    public function test_signup_with_no_phone_number(): void
    {
        $this->assertDatabaseEmpty('users');

        $response = $this->post(route('signup'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'phone' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('home')
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'email', 'phone', 'password'])
            ->assertSessionHas('success', 'Account created successfully. Please check your email to verify your account.');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'phone' => null,
        ]);
    }

    public function test_signup_with_full_correct_data(): void
    {
        Notification::fake();
        $this->assertDatabaseEmpty('users');

        $response = $this->post(route('signup'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'phone' => '8889991234',
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('home')
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'email', 'phone', 'password'])
            ->assertSessionHas('success', 'Account created successfully. Please check your email to verify your account.');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'a@test.com',
            'phone' => '8889991234',
            'email_verified_at' => null,
        ]);

        // Test verification email
        $user = User::query()->where('email', '=', 'a@test.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user, &$verificationUrl) {
            // Extract the verification URL from the notification
            $verificationUrl = $notification->toMail($user)->actionUrl;

            // You can do assertions here too
            return str_contains($verificationUrl, '/email/verify/');
        });
    }

    public function test_redirect_status_of_not_authenticated_for_logout(): void
    {
        $response = $this->post(route('logout'));

        $response->assertStatus(302);
        $response->assertRedirectToRoute('home');
    }
}
