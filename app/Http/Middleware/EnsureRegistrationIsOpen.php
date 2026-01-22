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
        // Si ya existe al menos un usuario, prohibimos el acceso al registro público
        if (User::count() > 0) {
            // Opción A: Redirigir al login con mensaje
            return redirect()->route('login')->with('status', 'El registro público está cerrado. Contacta con el administrador.');

            // Opción B: Error 403 (Prohibido)
            // abort(403, 'El registro de usuarios está cerrado.');
        }

        return $next($request);
    }
}
