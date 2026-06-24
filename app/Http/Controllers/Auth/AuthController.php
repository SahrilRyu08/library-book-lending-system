<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * AuthController - DUMMY untuk preview UI
 * Login/register tidak benar-benar cek database.
 * Ganti isi method ini dengan logic Auth::attempt() saat implementasi nyata.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // --- DUMMY: langsung redirect sesuai "role" yang diketik ---
        // Saat implementasi nyata, ganti dengan Auth::attempt()
        if ($request->email === 'admin@moco.app') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('member.books.index');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // DUMMY: langsung ke halaman member
        return redirect()->route('member.books.index');
    }

    public function logout(Request $request)
    {
        return redirect()->route('login');
    }
}
