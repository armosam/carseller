<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            switch ($provider) {
                case 'facebook':
                    $field = 'facebook_id';
                    break;
                case 'google':
                    $field = 'google_id';
                    break;
                case 'twitter':
                    $field = 'twitter_id';
                    break;
                default:
                    $field = null;
            }

            $user = Socialite::driver($provider)->user();
            $dbUser = User::query()->where('email', $user->email)->first();

            if ($dbUser) {
                $dbUser->$field = $user->id;
                $dbUser->save();
            } else {
                [$first_name, $last_name] = explode(' ', $user->getName());
                $dbUser = User::query()->create([
                    'email' => $user->email,
                    'first_name' => trim($first_name),
                    'last_name' => trim($last_name),
                    $field => $user->id,
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($dbUser);

            return redirect()->intended(route('home'));
        } catch (Exception $e) {
            return redirect(route('login'))
                ->with('error', $e->getMessage() ?: 'Something went wrong.');
        }
    }
}
