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
        if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'Aslab') {
            return $next($request);
        }

        return redirect()->route('login.aslab')->withErrors([
            'npm' => 'Sesi Anda tidak valid atau Anda tidak memiliki akses ke halaman aslab.',
        ]);
    }
}
