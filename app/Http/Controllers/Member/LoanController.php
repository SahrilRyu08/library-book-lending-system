<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private function makeLoan(int $id, string $judul, string $tglPinjam, string $tglKembali, string $status, int $denda = 0): \stdClass
    {
        $loan = new \stdClass();
        $loan->id              = $id;
        $loan->tanggal_pinjam  = $tglPinjam;
        $loan->tanggal_kembali = $tglKembali;
        $loan->status          = $status;
        $loan->denda           = $denda;

        $buku   = new \stdClass(); $buku->judul = $judul;
        $det    = new \stdClass(); $det->buku   = $buku;
        $col    = collect([$det]);
        $loan->detail = new class($col) {
            public function __construct(private $col) {}
            public function first() { return $this->col->first(); }
        };

        return $loan;
    }

    // Peminjaman on-going
    public function index()
    {
        $aktifLoans = collect([
            $this->makeLoan(1, 'Laskar Pelangi', '2026-06-10', '2026-06-24', 'dipinjam'),
            $this->makeLoan(2, 'Atomic Habits',  '2026-06-15', '2026-06-22', 'dipinjam'),
        ]);

        return view('member.loans.index', [
            'aktifLoans' => $aktifLoans,
            'aktif'      => $aktifLoans->count(),
            'maxPinjam'  => 3,
        ]);
    }

    // Riwayat yang sudah selesai
    public function history()
    {
        $history = collect([
            $this->makeLoan(3, 'Bumi Manusia',   '2026-05-01', '2026-05-14', 'dikembalikan', 0),
            $this->makeLoan(4, 'Filosofi Teras', '2026-04-20', '2026-04-30', 'dikembalikan', 5000),
            $this->makeLoan(5, 'Deep Work',      '2026-03-10', '2026-03-20', 'dikembalikan', 0),
        ]);

        $page = request()->get('page', 1);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $history->forPage($page, 10), $history->count(), 10, $page,
            ['path' => request()->url()]
        );

        return view('member.loans.history', [
            'historyLoans' => $paginator,
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->route('member.loans.index')
                         ->with('success', 'Buku berhasil dipinjam!');
    }

    public function show($id)
    {
        return redirect()->route('member.loans.index');
    }
}
