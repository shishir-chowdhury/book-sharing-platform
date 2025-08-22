<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        /** @var \App\Models\User $u */
        $u = auth()->user();
        if (! $u || ! $u->is_admin) {
            return response()->json(['message' => 'Forbidden. Admin only.'], 403);
        }
        return $next($request);
    }
}
