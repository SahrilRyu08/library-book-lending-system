<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Cek apakah user yang login memiliki role yang sesuai
     * Dipakai di route group:
     *   - middleware('role:admin')   → hanya admin yang bisa akses
     *   - middleware('role:anggota') → hanya anggota yang bisa akses
     *
     * Kalau belum login → redirect ke halaman login
     * Kalau role tidak sesuai → abort 403 Forbidden
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
