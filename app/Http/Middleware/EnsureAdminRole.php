<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has valid role (brand_owner, store_manager, or kasir)
        if (!in_array($user->role, ['brand_owner', 'store_manager', 'kasir'])) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke dashboard admin.');
        }

        return $next($request);
    }
}
