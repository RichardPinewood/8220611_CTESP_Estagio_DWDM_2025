<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is a client (Client model)
        if (!auth('client')->check() || !auth('client')->user() instanceof \App\Models\Client) {
            abort(403, 'Access denied. Client access only.');
        }

        return $next($request);
    }
}