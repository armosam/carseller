<?php

namespace App\Http\Controllers;

use App\Http\Requests\Session\PasswordResetRequest;
use App\Http\Requests\Session\StorePasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Nette\Utils\Random;

class AuthController extends Controller
{
    /**
     * Open password reset form
     */
    public function passwordResetRequest(): View
    {
        Auth::logout();
        return view('auth.password-reset-request');
    }

    /**
     * Store a newly created resource in storage and send email.
     */
    public function storePasswordReset(PasswordResetRequest $request)
    {
        $attributes = $request->validated();

        // Send verification email for password change
        $status = Password::sendResetLink($attributes);

        // If email was not sent redirect user back with error message
        // When you don't need to show that account doesn't exist with that email
        // then instead of checking simply redirect with successful message that email was sent
        if ($status !== Password::RESET_LINK_SENT) {
            return redirect()->back()
                ->withErrors(['email' => __($status)])
                ->withInput($request->only('email'));
        }

        // If email was sent then redirect back with success message
        return redirect()->back()->with('success', __($status));
    }

    /**
     * Open set password form
     */
    public function passwordReset(Request $request, string $token): View
    {
        Auth::logout();
        $attributes = array_merge($request->all(), ['token' => $token]);

        // To validate token create Rule that will use Hash::check() to validate with DB token
        $validator = Validator::make($attributes, [
            'email' => 'required|email|exists:password_reset_tokens,email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        return view('auth.password-reset', ['token' => $token, 'email' => $attributes['email']]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storePassword(StorePasswordRequest $request)
    {
        $request->validated();

        // Reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return redirect()->back()->withErrors(['email' => __($status)]);
        }

        return to_route('login')->with('success', __($status));
    }
}
