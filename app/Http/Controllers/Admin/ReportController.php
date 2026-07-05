<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Admin ReportController - DUMMY untuk preview UI
 */
class ReportController extends Controller
{
    private function makeLoan(int $id, string $nama, string $judul, string $pinjam, string $kembali, int $denda): \stdClass
    {
        $loan = new \stdClass();
        $loan->id = $id; $loan->denda = $denda;
        $loan->tanggal_pinjam = $pinjam;
        $loan->tanggal_kembali_aktual = $kembali;

        $user = new \stdClass(); $user->nama = $nama; $loan->user = $user;

        $buku = new \stdClass(); $buku->judul = $judul;
        $det  = new \stdClass(); $det->buku   = $buku;
        $col  = collect([$det]);
        $loan->detail = new class($col) {
            public function __construct(private $col) {}
            public function first() { return $this->col->first(); }
        };

        return $loan;
    }

    public function index(Request $request)
    {
        $allLoans = collect([
            $this->makeLoan(1, 'John Doe',   'Laskar Pelangi', '2026-06-10', '2026-06-20', 0),
            $this->makeLoan(2, 'Jane Smith', 'Atomic Habits',  '2026-06-01', '2026-06-20', 5000),
            $this->makeLoan(3, 'Budi S.',    'Bumi Manusia',   '2026-05-14', '2026-05-28', 0),
            $this->makeLoan(4, 'Ani R.',     'Deep Work',      '2026-05-01', '2026-05-20', 10000),
            $this->makeLoan(5, 'Citra M.',   'Sapiens',        '2026-06-05', '2026-06-18', 0),
        ]);

        $page = $request->get('page', 1);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $allLoans->forPage($page, 10), $allLoans->count(), 10, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.reports.index', [
            'loans'          => $paginator,
            'totalTransaksi' => 87,
            'totalDenda'     => 145000,
            'jumlahTerlambat'=> 6,
        ]);
    }
}
