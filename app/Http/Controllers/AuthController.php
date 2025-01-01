<?php

namespace App\Http\Controllers;

use App\Http\Requests\Session\PasswordResetRequest;
use App\Http\Requests\Session\StorePasswordRequest;

class AuthController extends Controller
{
    /**
     * Open password reset form
     */
    public function passwordReset()
    {
        return view('auth.password-reset');
    }

    /**
     * Store a newly created resource in storage and send email.
     */
    public function storePasswordReset(PasswordResetRequest $request)
    {
        $attributes = $request->validated();
        dd($attributes);
        // Send verification email for password change
    }

    /**
     * Open set password form
     */
    public function setPassword()
    {
        return view('auth.set-password');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storePassword(StorePasswordRequest $request)
    {
        $attributes = $request->validated();
        dd($attributes);
        // store password
    }
}
