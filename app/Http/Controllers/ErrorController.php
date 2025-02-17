<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $env = config('app.env', 'production');
        if ($env === 'production') {
            return 'Error happened.  Try again later.';
        } else {
            return view('errors.404');
        }
    }
}
