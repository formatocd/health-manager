<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ForceAdminRegistration
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Schema::hasTable('users')) {
            return $next($request);
        }

        if (User::exists()) {
            return $next($request);
        }

        if ($request->routeIs('register') || $request->is('register') || $request->is('livewire/*')) {
            return $next($request);
        }

        return redirect()->route('register');
    }
}
