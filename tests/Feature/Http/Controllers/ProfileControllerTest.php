<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    // Will migrate database for each test
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_not_authenticated_access_redirection_to_login_page(): void
    {
        $response = $this->get(route('profile.index'));
        $response
            ->assertStatus(302)
            ->assertRedirect(route('login'))
            ->assertDontSee('My Profile');
    }

    public function test_authenticated_access_response_status(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('profile.index'));
        $response
            ->assertStatus(200)
            ->assertSee('Logout')
            ->assertSee('My Profile')
            ->assertSeeHtml('type="email" name="email" value="'.$user->email.'"')
            ->assertSeeHtml('type="text" name="first_name" value="'.$user->first_name.'"')
            ->assertSeeHtml('type="text" name="last_name" value="'.$user->last_name.'"')
            ->assertSeeHtml('type="phone" name="phone" value="'.$user->phone.'"')
            ->assertSeeHtml('>Update</button>')

            ->assertSeeHtml('input type="password" name="current_password" placeholder="Current Password"')
            ->assertSeeHtml('input type="password" name="password" placeholder="New Password"')
            ->assertSeeHtml('input type="password" name="password_confirmation" placeholder="Repeat Password"')
            ->assertSeeHtml('>Update Password</button>');

        /*$response->dump();
        $response->dumpSession();
        $response->dumpHeaders();*/
    }

    public function test_authenticated_access_with_social_login(): void
    {
        $user = User::factory()->create([
            'facebook_id' => '123456789',
            'password' => null,
        ]);
        $response = $this->actingAs($user)->get(route('profile.index'));
        $response
            ->assertStatus(200)
            ->assertSee('Logout')
            ->assertSee('My Profile')
            ->assertSeeHtml('type="email" name="email" value="'.$user->email.'"')
            ->assertSeeHtml('type="text" name="first_name" value="'.$user->first_name.'"')
            ->assertSeeHtml('type="text" name="last_name" value="'.$user->last_name.'"')
            ->assertSeeHtml('type="phone" name="phone" value="'.$user->phone.'"')
            ->assertSeeHtml('>Update</button>')

            ->assertDontSeeHtml('input type="password" name="current_password" placeholder="Current Password"')
            ->assertDontSeeHtml('input type="password" name="password" placeholder="New Password"')
            ->assertDontSeeHtml('input type="password" name="password_confirmation" placeholder="Repeat Password"')
            ->assertDontSeeHtml('>Update Password</button>');
    }

    public function test_store_profile_with_empty_data(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => '',
            'first_name' => '',
            'last_name' => '',
            'phone' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['phone'])
            ->assertInvalid([
                'first_name' => ['The first name field is required.'],
                'last_name' => ['The last name field is required.'],
                'email' => ['The email field is required.'],
            ]);
    }

    public function test_store_profile_with_empty_first_name(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => 'aaa@test.com',
            'first_name' => '',
            'last_name' => 'last_name',
            'phone' => '00123456789',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['email', 'last_name', 'phone'])
            ->assertInvalid([
                'first_name' => ['The first name field is required.'],
            ]);
    }

    public function test_store_profile_with_empty_last_name(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => 'aaa@test.com',
            'first_name' => 'first_name',
            'last_name' => '',
            'phone' => '00123456789',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['email', 'first_name', 'phone'])
            ->assertInvalid([
                'last_name' => ['The last name field is required.'],
            ]);
    }

    public function test_store_profile_successfully_with_empty_phone(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => $user->email,
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'phone' => '',
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('profile.index')
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'phone', 'email'])
            ->assertSessionHas('success', 'Your profile was updated.');
    }

    public function test_store_profile_with_incorrect_phone(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => $user->email,
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'phone' => '123',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['email', 'first_name', 'last_name'])
            ->assertInvalid([
                'phone' => ['The phone must be 11 numeric characters.'],
            ]);
    }

    public function test_store_profile_with_empty_email_address(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => '',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'phone' => '00123456789',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'phone'])
            ->assertInvalid([
                'email' => ['The email field is required.'],
            ]);
    }

    public function test_store_profile_with_invalid_email_address(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => 'invalid_email',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'phone' => '00123456789',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'phone'])
            ->assertInvalid([
                'email' => ['The email field must be a valid email address.'],
            ]);
    }

    public function test_store_profile_successfully_with_different_valid_email_address(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'email' => 'aaa@test.com',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'phone' => '00123456789',
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('profile.index')
            ->assertSessionDoesntHaveErrors(['first_name', 'last_name', 'phone', 'email'])
            ->assertSessionHas('success', 'Your profile was updated. Email verification message was sent.');

        // Ensure email verification notification was sent when email was changed
        Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user) {
            $this->assertEquals('Verify Email Address', $notification->toMail($user)->subject);
            $this->assertEquals('Verify Email Address', $notification->toMail($user)->actionText);
            $this->assertEquals(['Please click the button below to verify your email address.'], $notification->toMail($user)->introLines);
            $this->assertStringContainsString('/email/verify/', $notification->toMail($user)->actionUrl);

            return true;
        });
    }

    public function test_store_profile_with_empty_current_password(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'password_confirmation'])
            ->assertInvalid([
                'current_password' => ['The current password field is required.'],
            ]);
    }

    public function test_store_profile_with_incorrect_current_password(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => '123',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['password', 'password_confirmation'])
            ->assertInvalid([
                'current_password' => ['The current password field must be at least 8 characters.'],
            ]);
    }

    public function test_store_profile_with_wrong_current_password(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => 'wrong_current_password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('profile.index')
            ->assertSessionHas('error', 'Your current password is incorrect.');
    }

    public function test_store_profile_with_empty_new_password(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => 'password',
            'password' => '',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['current_password', 'password_confirmation'])
            ->assertInvalid([
                'password' => ['The password field is required.'],
            ]);
    }

    public function test_store_profile_with_incorrect_new_password(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => 'password',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['current_password', 'password_confirmation'])
            ->assertInvalid([
                'password' => ['The password field must be at least 8 characters.'],
            ]);
    }

    public function test_store_profile_with_not_matching_with_confirmation_password(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => 'password',
            'password' => 'newPassword',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(302)
            ->assertRedirectBack()
            ->assertSessionDoesntHaveErrors(['current_password', 'password_confirmation'])
            ->assertInvalid([
                'password' => ['The password field confirmation does not match.'],
            ]);
    }

    public function test_store_profile_successfully_with_correct_current_new_confirmation_passwords(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => 'password',
            'password' => 'newPassword',
            'password_confirmation' => 'newPassword',
        ]);

        $response->assertStatus(302)
            ->assertRedirectToRoute('profile.index')
            ->assertSessionDoesntHaveErrors(['current_password', 'password', 'password_confirmation'])
            ->assertSessionHas('success', 'Your password was updated.');
    }
}
