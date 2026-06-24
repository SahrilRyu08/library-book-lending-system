<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    private function makeReturn(int $id, string $nama, string $judul, string $pinjam, string $kembali, int $denda): \stdClass
    {
        $loan = new \stdClass();
        $loan->id              = $id;
        $loan->tanggal_pinjam  = $pinjam;
        $loan->tanggal_kembali = $kembali;
        $loan->denda           = $denda;
        $loan->status          = 'dikembalikan';

        $user = new \stdClass();
        $user->nama = $nama;
        $loan->user = $user;

        $buku = new \stdClass();
        $buku->judul = $judul;
        $det  = new \stdClass();
        $det->buku   = $buku;
        $col  = collect([$det]);
        $loan->detail = new class($col) {
            public function __construct(private $col) {}
            public function first() { return $this->col->first(); }
        };

        return $loan;
    }

    public function index(Request $request)
    {
        $returns = collect([
            $this->makeReturn(1, 'John Doe',   'Laskar Pelangi', '2026-05-01', '2026-05-14', 0),
            $this->makeReturn(2, 'Jane Smith', 'Atomic Habits',  '2026-05-10', '2026-05-20', 5000),
            $this->makeReturn(3, 'Budi S.',    'Bumi Manusia',   '2026-05-14', '2026-05-30', 0),
            $this->makeReturn(4, 'Ani R.',     'Deep Work',      '2026-04-20', '2026-05-05', 10000),
            $this->makeReturn(5, 'Citra M.',   'Sapiens',        '2026-05-05', '2026-05-16', 0),
        ]);

        if ($request->status === 'terlambat') {
            $returns = $returns->filter(fn($l) => $l->denda > 0);
        } elseif ($request->status === 'tepat') {
            $returns = $returns->filter(fn($l) => $l->denda == 0);
        }

        $page = $request->get('page', 1);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $returns->values(), $returns->count(), 10, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.returns.index', ['returns' => $paginator]);
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.returns.index')
                         ->with('success', 'Pengembalian berhasil dicatat! Denda dihitung otomatis.');
    }
}
