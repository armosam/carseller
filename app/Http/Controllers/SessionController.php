<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\Session\LoginRequest;
use App\Http\Requests\Session\RegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    /**
     * Open signup form
     */
    public function signup()
    {
        return view('auth.signup');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function registration(RegistrationRequest $request)
    {
        $attributes = $request->validated();
        $user = User::query()->create($attributes);
        // Send email verification message. It logs user in directly now.
        Auth::login($user);
        return redirect()->route('home');
    }
    /**
     * Open login form
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate user input and login the user
     * @param LoginRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function authentication(LoginRequest $request)
    {
        $attributes = $request->validated();

        if (!Auth::attempt($attributes)) {
            throw ValidationException::withMessages([
                'email' => 'User credentials do not match our records.'
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
