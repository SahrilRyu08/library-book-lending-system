<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Dashboard
        $bookCount = Buku::count();

        $activeLoan = Peminjaman::where('status', 'dipinjam')
            ->count();

        $lateLoan = Peminjaman::where('status', 'terlambat')
            ->count();

        $memberCount = User::where('role', 'member')
            ->count();

        // Buku Terpopuler
        $popularBooks = PeminjamanDetail::select(
            'buku_id',
            DB::raw('SUM(jumlah) as total_dipinjam')
        )
            ->with([
                'buku.kategori'
            ])
            ->groupBy('buku_id')
            ->orderByDesc('total_dipinjam')
            ->take(5)
            ->get()
            ->map(function ($detail) {

                $buku = $detail->buku;

                $buku->total_dipinjam = $detail->total_dipinjam;

                return $buku;
            });

        return view('admin.dashboard', compact(
            'bookCount',
            'lateLoan',
            'activeLoan',
            'memberCount',
            'popularBooks'
        ));
    }
}
