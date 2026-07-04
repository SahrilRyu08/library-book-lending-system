<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $query = Peminjaman::with([
            'user',
            'detail.buku',
        ]);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pinjam', [
                $startDate,
                $endDate,
            ]);
        }

        $loans = (clone $query)
            ->latest('tanggal_pinjam')
            ->paginate(10)
            ->withQueryString();

        $totalTransaksi = (clone $query)->count();
        $totalDenda     = (clone $query)->sum('denda');

        // Catatan: 'terlambat' di sini dihitung dari transaksi yang
        // pernah telat (denda > 0), bukan status 'terlambat' saja,
        // supaya konsisten dengan ReturnController yang selalu
        // menutup transaksi dengan status 'selesai'.
        $jumlahTerlambat = (clone $query)
            ->where(function ($q) {
                $q->where('status', 'terlambat')
                    ->orWhere('denda', '>', 0);
            })
            ->count();

        $popularBooks = PeminjamanDetail::select(
            'buku_id',
            DB::raw('SUM(jumlah) as total_dipinjam')
        )
            ->with('buku')
            ->groupBy('buku_id')
            ->orderByDesc('total_dipinjam')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'loans',
            'totalTransaksi',
            'totalDenda',
            'jumlahTerlambat',
            'popularBooks',
            'startDate',
            'endDate'
        ));
    }
}
