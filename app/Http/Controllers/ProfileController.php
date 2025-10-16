<?php

namespace App\Http\Controllers;

use App\Rules\PhoneRule;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request): Application|Redirector|RedirectResponse
    {
        $user = $request->user();
        $rules = [
            'first_name' => ['required', 'string', 'min:3', 'max:20'],
            'last_name' => ['required', 'string', 'min:3', 'max:20'],
            'phone' => [
                'nullable',
                new PhoneRule, // instantiate your Phone rule
                Rule::unique('users', 'phone')->ignore($user->id), // unique in users excluding the current user's record
            ],
        ];

        if (! $user->isOauthUser()) {
            $rules['email'] = ['required', 'email', 'unique:users,email,'.$user->id]; // unique in users excluding the current user's record
        }

        $data = $request->validate($rules);

        $user->fill($data);

        $message = 'Your profile was updated.';

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
            $message = 'Your profile was updated. Email verification message was sent.';
        }

        $user->save();

        return redirect(route('profile.index'))->with('success', $message);
    }

    public function updatePassword(Request $request): Application|Redirector|RedirectResponse
    {
        $user = $request->user();

        if ($user->isOauthUser()) {
            return redirect(route('profile.index'))->with('error', 'You signed up by 3-rd party providers. You cannot set or update your password.');
        }

        $rules = [
            'current_password' => ['required', 'string', 'min:8'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];

        $data = $request->validate($rules);

        if (! Hash::check($data['current_password'], $user->password)) {
            return redirect(route('profile.index'))->with('error', 'Your current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect(route('profile.index'))->with('success', 'Your password was updated.');
    }
}
