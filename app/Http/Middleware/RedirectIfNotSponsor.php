<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotSponsor
{
    public function handle(Request $request, Closure $next)
    {
        // Already logged in as sponsor — allow through
        if (Auth::guard('sponsor')->check()) {
            return $next($request);
        }

        // Logged in as admin — redirect to dashboard
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. Sponsor only.');
        }

        // Not logged in at all — redirect to sponsor login
        return redirect()->route('sponsor.login')
            ->with('error', 'Please log in as a sponsor to continue.');
    }
}