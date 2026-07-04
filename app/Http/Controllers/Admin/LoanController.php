<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private function makeLoan(int $id, string $nama, string $email, string $judul, string $penulis, string $tglPinjam, string $tglKembali, int $dendaPerHari = 1000): \stdClass
    {
        $loan = new \stdClass();
        $loan->id              = $id;
        $loan->tanggal_pinjam  = $tglPinjam;
        $loan->tanggal_kembali = $tglKembali;
        $loan->status          = 'dipinjam';
        $loan->denda           = 0;

        $user = new \stdClass();
        $user->nama  = $nama;
        $user->email = $email;
        $user->active_loans = 2;
        $loan->user = $user;

        $buku = new \stdClass();
        $buku->judul   = $judul;
        $buku->penulis = $penulis;

        $det = new \stdClass();
        $det->buku   = $buku;
        $det->jumlah = 1;

        $col = collect([$det]);
        $loan->detail = new class($col) {
            public function __construct(private $col) {}
            public function first() { return $this->col->first(); }
            public function getIterator() { return $this->col->getIterator(); }
        };

        return $loan;
    }

    private function allLoans(): \Illuminate\Support\Collection
    {
        return collect([
            $this->makeLoan(1, 'John Doe',   'john@mail.com',  'Laskar Pelangi', 'Andrea Hirata',     '2026-06-10', '2026-06-24'),
            $this->makeLoan(2, 'Jane Smith', 'jane@mail.com',  'Atomic Habits',  'James Clear',       '2026-06-01', '2026-06-15'),
            $this->makeLoan(3, 'Budi S.',    'budi@mail.com',  'Bumi Manusia',   'Pramoedya A.T.',    '2026-06-05', '2026-06-26'),
            $this->makeLoan(4, 'Ani R.',     'ani@mail.com',   'Deep Work',      'Cal Newport',       '2026-05-28', '2026-06-11'),
            $this->makeLoan(5, 'Citra M.',   'citra@mail.com', 'Sapiens',        'Yuval Noah Harari', '2026-06-12', '2026-06-22'),
        ]);
    }

    public function index(Request $request)
    {
        $loans = $this->allLoans();

        if ($request->search) {
            $q = strtolower($request->search);
            $loans = $loans->filter(fn($l) =>
                str_contains(strtolower($l->user->nama), $q) ||
                str_contains(strtolower($l->detail->first()->buku->judul), $q)
            );
        }

        if ($request->status === 'terlambat') {
            $loans = $loans->filter(fn($l) =>
                \Carbon\Carbon::now()->diffInDays($l->tanggal_kembali, false) < 0
            );
        } elseif ($request->status === 'mendekati') {
            $loans = $loans->filter(function($l) {
                $d = \Carbon\Carbon::now()->diffInDays($l->tanggal_kembali, false);
                return $d >= 0 && $d <= 3;
            });
        } elseif ($request->status === 'aman') {
            $loans = $loans->filter(fn($l) =>
                \Carbon\Carbon::now()->diffInDays($l->tanggal_kembali, false) > 3
            );
        }

        $page = $request->get('page', 1);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $loans->values(), $loans->count(), 10, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.loans.index', ['loans' => $paginator]);
    }

    public function show($id)
    {
        $loan   = $this->allLoans()->firstWhere('id', (int) $id)
                  ?? $this->allLoans()->first();
        $isDone = false;

        return view('admin.loans.show', compact('loan', 'isDone'));
    }
}
