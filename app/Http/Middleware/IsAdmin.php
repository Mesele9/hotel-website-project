<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and if their email matches the admin's email.
        if (Auth::check() && Auth::user()->email === 'admin@hotel.com') {
            return $next($request);
        }

        // If not an admin, abort the request and show a 403 Forbidden error.
        abort(403, 'Unauthorized action.');
    }
}