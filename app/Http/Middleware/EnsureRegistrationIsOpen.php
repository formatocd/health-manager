<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureRegistrationIsOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (User::count() > 0) {
            return redirect()->route('login')->with('status', 'El registro público está cerrado. Contacta con el administrador.');
        }

        return $next($request);
    }
}
