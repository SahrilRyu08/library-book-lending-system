<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Notifications\KeterlambatanNotification;
use App\Notifications\PengembalianNotification;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function __construct(protected LoanService $loanService)
    {
    }

    /**
     * Daftar peminjaman yang belum dikembalikan (dipinjam / terlambat).
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with([
            'user',
            'detail.buku',
        ])
            ->whereIn('status', ['dipinjam', 'terlambat']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('member')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->member . '%');
            });
        }

        if ($request->filled('book')) {
            $query->whereHas('detail.buku', function ($q) use ($request) {
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
     * Proses pengembalian buku - sudah pakai LoanService (Branch 4).
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:peminjaman,id',
        ]);

        $loan = Peminjaman::with('details.buku', 'user')
            ->findOrFail($request->loan_id);

        if ($loan->tanggal_kembali !== null || $loan->status === 'selesai') {
            return back()->with(
                'error',
                'Peminjaman ini sudah pernah dikembalikan.'
            );
        }

        $isTerlambat = false;

        DB::transaction(function () use ($loan, &$isTerlambat) {
            $loan->tanggal_kembali = now();

            // Hitung denda pakai LoanService (bukan manual lagi)
            $denda = $this->loanService->hitungDenda($loan);
            $isTerlambat = $denda > 0;

            $loan->denda  = $denda;
            $loan->status = 'selesai';
            $loan->save();

            foreach ($loan->details as $detail) {
                $detail->buku->increment('stok');
            }
        });

        // Kirim notifikasi
        $loan->user->notify(new PengembalianNotification($loan));
        if ($isTerlambat) {
            $loan->user->notify(new KeterlambatanNotification($loan));
        }

        return redirect()
            ->route('admin.returns.index')
            ->with('success', 'Pengembalian berhasil diproses.');
    }
}
