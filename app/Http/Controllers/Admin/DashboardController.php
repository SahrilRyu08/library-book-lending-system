<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBuku = Buku::sum('stok');
        $sedangDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $terlambat = Peminjaman::where('status', 'dipinjam')
                                ->whereDate('jatuh_tempo', '<', Carbon::now())
                                ->count();
        $totalAnggota = User::where('role', 'member')->count();

        // Popular books (most borrowed)
        $popularBooks = Buku::with('kategori')
                            ->withCount(['peminjamanDetail as total_dipinjam' => function($q) {
                                $q->select(\DB::raw('count(*)'));
                            }])
                            ->orderBy('total_dipinjam', 'desc')
                            ->limit(5)
                            ->get();

        return view('admin.dashboard', [
            'totalBuku' => $totalBuku,
            'sedangDipinjam' => $sedangDipinjam,
            'terlambat' => $terlambat,
            'totalAnggota' => $totalAnggota,
            'popularBooks' => $popularBooks,
        ]);
    }
}
