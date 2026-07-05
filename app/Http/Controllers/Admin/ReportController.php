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
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ]);
        }

        $loans = (clone $query)
            ->latest('tanggal_pinjam')
            ->paginate(10)
            ->withQueryString();

        $totalTransaksi = (clone $query)->count();
        $totalDenda     = (clone $query)->sum('denda');

        // Hitung transaksi yang saat ini statusnya 'terlambat'
        $jumlahTerlambat = (clone $query)
            ->whereNull('tanggal_kembali')
            ->where('status', 'terlambat')
            ->count();

        $popularBooksQuery = PeminjamanDetail::select(
            'buku_id',
            DB::raw('SUM(jumlah) as total_dipinjam')
        )
            ->with('buku');

        if ($startDate && $endDate) {
            $popularBooksQuery->whereHas('peminjaman', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_pinjam', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59',
                ]);
            });
        }

        $popularBooks = $popularBooksQuery
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
