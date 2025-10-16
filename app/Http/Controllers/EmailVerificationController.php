<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, string $id, string $hash)
    {
        $user = User::query()->findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return back()->withErrors(['Invalid verification link.']);
        }

        // After verification could log in
        // Auth::login($user);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', 'Your email already verified.');
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return redirect()->route('home')->with('success', 'Your email now verified.');
    }

    public function notice(Request $request)
    {
        return view('auth.verify-email');
    }

    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent!');
    }
}
