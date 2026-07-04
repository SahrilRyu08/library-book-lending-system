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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user is admin (based on email or role)
        $user = auth()->user();
        if ($user->email !== 'admin@moco.app' && $user->email !== 'admin@library.com' && ($user->role ?? null) !== 'admin') {
            return redirect()->route('member.books.index')->with('error', 'Anda tidak memiliki akses ke area admin');
        }

        return $next($request);
    }
}
