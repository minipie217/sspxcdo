<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
    public function handle(Request $request, Closure $next)
    {
       
        // Already logged in as admin — allow through
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        // Logged in as sponsor — redirect to raffle index
        if (Auth::guard('sponsor')->check()) {
            return redirect()->route('raffle.index')
                ->with('error', 'Access denied. Admin only.');
        }

        // Not logged in at all — redirect to admin login
        return redirect()->route('admin.login')
            ->with('error', 'Please log in as an admin to continue.');
    }
}