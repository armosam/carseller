<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\Session\LoginRequest;
use App\Http\Requests\Session\RegistrationRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    /**
     * Open signup form
     */
    public function signup()
    {
        Auth::logout();
        return view('auth.signup');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function registration(RegistrationRequest $request)
    {
        $attributes = $request->validated();
        $user = User::query()->create($attributes);

        // Send email verification message.
        event(new Registered($user));

        //Auth::login($user);
        return redirect()->route('home')->with('success', 'Account created successfully. Please check your email to verify your account.');
    }
    /**
     * Open login form
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('car.index');
        }
        return view('auth.login');
    }

    /**
     * Authenticate user input and login the user
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function authentication(LoginRequest $request)
    {
        $attributes = $request->validated();

        if (!Auth::attempt($attributes)) {
            /*throw ValidationException::withMessages([
                'email' => 'User credentials do not match our records.'
            ]);*/
            return redirect()->back()->withErrors([
                'email' => 'User credentials do not match our records'
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended('car')->with('success', 'Welcome back ' . Auth::user()->fullName());
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Regenerate session
        $request->session()->regenerate();
        // Regenerate csrf token
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
