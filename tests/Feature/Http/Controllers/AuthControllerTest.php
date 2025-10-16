<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_password_reset_request_page_response_status(): void
    {
        $response = $this->get('/auth/password-reset-request');

        $response->assertStatus(200)
            ->assertViewIs('auth.password-reset-request')
            ->assertViewHas('errors', null)
            ->assertSee('<h1 class="auth-page-title">Password Reset</h1>', false)
            ->assertSee('Reset Password</button>', false)
            ->assertSee('<input type="email" name="email" placeholder="Your Email"', false)
            ->assertSee("Don't need to reset your password?", false)
            ->assertSee('<a href="'.route('login').'">Click here to login</a>', false);
    }

    public function test_password_reset_request_with_empty_email(): void
    {
        $response = $this->post(route('password.storeResetRequest'), [
            'email' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionHasErrors(['email' => 'The email field is required.']);
    }

    public function test_password_reset_request_with_invalid_email(): void
    {
        $response = $this->post(route('password.storeResetRequest'), [
            'email' => 'invalid_email',
        ]);
        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionHasErrors(['email' => 'The email field must be a valid email address.']);
    }

    public function test_password_reset_request_with_not_existing_email(): void
    {
        User::factory()->create([
            'email' => 'aaa@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'aaa@test.com',
        ]);

        $response = $this->post(route('password.storeResetRequest'), [
            'email' => 'bbb@test.com',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionHasErrors(['email' => 'Provided email is not allowed.']);
    }

    public function test_password_reset_request_with_existing_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'aaa@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'aaa@test.com',
        ]);

        $response = $this->post(route('password.storeResetRequest'), [
            'email' => 'aaa@test.com',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionHas('success', 'We have emailed your password reset link.');

        // Assert notification sent to user after password reset submitted
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {

            $email = $user->email;
            $token = $notification->token;

            $this->assertEquals('Reset Password Notification', $notification->toMail($user)->subject, 'Subject in the email does not match');
            $this->assertNotNull($token, 'Token is missing in the email notification.');

            $emailedResetUrl = urldecode($notification->toMail($user)->actionUrl);
            $this->assertNotNull($emailedResetUrl, 'Link is missing in the email sent to the user.');

            // Assert that the token and email are in the reset link
            $this->assertStringContainsString($email, $emailedResetUrl, 'The email address is missing in the password reset link.');
            $this->assertStringContainsString($token, $emailedResetUrl, 'The token is missing in the password reset link.');

            // Assert that password reset token and email stored in the database
            $this->assertDatabaseHas('password_reset_tokens', [
                'email' => $email,
            ]);

            $token_record = DB::query()
                ->from('password_reset_tokens')
                ->where('email', $email)
                ->first();

            $this->assertNotNull($token_record, 'The token not found in the database.');

            // Assert that database token and emailed token match
            $this->assertTrue(Hash::check($token, $token_record->token),
                'Hashed token in the database does not match the token in the email.');

            return true;
        });
    }

    public function test_password_reset_page_response_404_with_empty_email(): void
    {
        $email = '';
        $token = '123TOKEN123';
        $response = $this->get(route('password.reset', [$token, 'email' => $email]));

        $response->assertStatus(404);
        $response->assertSee('Not Found');
    }

    public function test_password_reset_page_response_404_with_invalid_email(): void
    {
        $email = 'invalid_email';
        $token = '123TOKEN123';
        $response = $this->get(route('password.reset', [$token, 'email' => $email]));

        $response->assertStatus(404);
        $response->assertSee('Not Found');
    }

    public function test_password_reset_page_response_404_with_empty_token(): void
    {
        $email = 'invalid_email';
        $token = ' ';
        $response = $this->get(route('password.reset', [$token, 'email' => $email]));

        $response->assertStatus(404);
        $response->assertSee('Not Found');
    }

    public function test_password_reset_page_response_404_with_invalid_token(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => now(),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->get(route('password.reset', ['INVALID_TOKEN', 'email' => $email]));

        $response->assertStatus(404);
        $response->assertSee('Not Found');
    }

    /**
     * When token is older than configured amount (60) minutes then it considers as expired
     */
    public function test_password_reset_page_response_404_with_expired_token(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') + 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->get(route('password.reset', [$token, 'email' => $email]));

        $response->assertStatus(404);
        $response->assertSee('Not Found');
    }

    public function test_password_reset_page_renders_with_valid_data(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') - 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->get(route('password.reset', [$token, 'email' => $email]));

        $response->assertStatus(200)
            ->assertSee('Password Reset | '.config('app_name'))
            ->assertSee('type="hidden" name="token" value="'.$token.'" autocomplete="off"', false)
            ->assertSee('type="hidden" name="email" value="'.$email.'" autocomplete="off"', false)
            ->assertSee('type="password" name="password" placeholder="New Password" autocomplete="off"', false)
            ->assertSee('type="password" name="password_confirmation" placeholder="Confirm Password" autocomplete="off"', false)
            ->assertSee("Don't need to reset your password?", false)
            ->assertSee('Set New Password')
            ->assertSee('<a href="'.route('login').'">Click here to login</a>', false);
    }

    public function test_store_password_reset_with_empty_email(): void
    {
        $email = '';
        $token = '123TOKEN123';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'token'])
            ->assertInvalid(['email' => 'The email field is required.']);
    }

    public function test_store_password_reset_with_invalid_email(): void
    {
        $email = 'invalid_email';
        $token = '123TOKEN123';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'token'])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);
    }

    public function test_store_password_reset_with_not_existing_email(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        DB::table('password_reset_tokens')->insert([
            'email' => 'bbb@test.com',
            'token' => bcrypt($token),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') - 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'bbb@test.com']);

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'token'])
            ->assertInvalid(['email' => 'The selected email is invalid.']);
    }

    public function test_store_password_reset_with_empty_token(): void
    {
        $email = 'aaa@test.com';
        $token = '';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt('123TOKEN123'),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') - 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'email'])
            ->assertInvalid(['token' => 'The password reset token is invalid or has expired.']);
    }

    public function test_store_password_reset_with_invalid_token(): void
    {
        $email = 'aaa@test.com';
        $token = 'invalid_token';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt('123TOKEN123'),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') - 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'email'])
            ->assertInvalid(['token' => 'The password reset token is invalid or has expired.']);
    }

    public function test_store_password_reset_with_expired_token(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') + 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'email'])
            ->assertInvalid(['token' => 'The password reset token is invalid or has expired.']);
    }

    public function test_store_password_reset_with_missing_password_confirmation(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';
        $password = 'New_Password';
        $password_confirmation = 'wrong';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') - 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['token', 'email'])
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);
    }

    public function test_store_password_reset_with_valid_data_but_not_existing_user(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') - 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['token', 'password'])
            ->assertInvalid(['email' => "We can't find a user with that email address"]);
    }

    public function test_store_password_reset_with_valid_data(): void
    {
        $email = 'aaa@test.com';
        $token = '123TOKEN123';
        $password = 'New_Password';
        $password_confirmation = 'New_Password';

        User::factory()->create(['email' => $email]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => $email]);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') - 1),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $email]);

        $response = $this->post(route('password.store'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('login')
            ->assertSessionDoesntHaveErrors(['email', 'token', 'password'])
            ->assertSessionHas('success', 'Your password has been reset.');
    }
}
