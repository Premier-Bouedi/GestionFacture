<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté OU n'est pas admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, "Action non autorisée. Accès réservé aux administrateurs.");
        }

        return $next($request);
    }
}
