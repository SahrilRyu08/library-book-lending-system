<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman form login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login — validasi input, cek kredensial via Auth::attempt
     * Redirect ke dashboard admin atau katalog member sesuai role
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Regenerate session untuk keamanan (cegah session fixation)
            $request->session()->regenerate();

            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.books.index');
        }

        // Kembalikan ke form login dengan pesan error
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    /**
     * Tampilkan halaman form registrasi
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi anggota baru
     * Role otomatis 'anggota', password di-hash sebelum disimpan
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:50'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'anggota',
        ]);

        // Auto login setelah registrasi berhasil
        Auth::login($user);

        return redirect()->route('member.books.index');
    }

    /**
     * Proses logout — hapus session dan redirect ke halaman login
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session dan regenerate CSRF token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
