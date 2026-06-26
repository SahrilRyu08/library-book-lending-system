<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    /**
     * Daftar peminjaman yang belum dikembalikan
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with([
            'user',
            'details.buku',
        ])
            ->whereIn('status', ['dipinjam', 'terlambat']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Cari nama anggota
        if ($request->filled('member')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->member . '%');
            });
        }

        // Cari judul buku
        if ($request->filled('book')) {
            $query->whereHas('details.buku', function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->book . '%');
            });
        }

        $returns = $query
            ->latest('tanggal_pinjam')
            ->paginate(10)
            ->withQueryString();

        return view('admin.returns.index', compact('returns'));
    }

    /**
     * Proses pengembalian buku
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:peminjaman,id',
        ]);

        $loan = Peminjaman::findOrFail($request->loan_id);

        // sementara (nanti dipindahkan ke LoanService)
        $loan->tanggal_kembali = now();

        if ($loan->jatuh_tempo && now()->gt($loan->jatuh_tempo)) {

            $hari = now()->diffInDays($loan->jatuh_tempo);

            $loan->status = 'terlambat';

            // contoh denda Rp1.000/hari
            $loan->denda = $hari * 1000;

        } else {

            $loan->status = 'selesai';

            $loan->denda = 0;
        }

        $loan->save();

        // kembalikan stok buku
        foreach ($loan->details as $detail) {

            $detail->buku->increment('stok');

        }

        return redirect()
            ->route('admin.returns.index')
            ->with('success', 'Pengembalian berhasil diproses.');
    }
}
