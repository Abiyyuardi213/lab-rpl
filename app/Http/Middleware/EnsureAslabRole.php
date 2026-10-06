<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAslabRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role) {
            $roleName = Auth::user()->role->name;
            $activeMode = session('active_mode', $roleName);

            if ($roleName === 'Aslab' && $activeMode === 'Aslab') {
                return $next($request);
            }
        }

        if (Auth::check()) {
            return redirect()->route('home');
        }

        return redirect()->route('login.aslab');
    }
}
