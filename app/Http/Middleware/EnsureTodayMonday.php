<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTodayMonday
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->merge(['EnsureTodayMonday' => false]);
        $dayOfWeek = now()->dayOfWeek;
        if ($dayOfWeek === 1) {
            $request->merge(['EnsureTodayMonday' => true]);
            return $next($request);
        }

        abort(403, 'Sorry. Those vehicles allowed to buy only Monday.');
    }
}
