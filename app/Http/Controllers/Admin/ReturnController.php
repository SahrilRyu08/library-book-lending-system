<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Notifications\KeterlambatanNotification;
use App\Notifications\PengembalianNotification;
use App\Services\LoanService;
use Carbon\Carbon;
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
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['dipinjam', 'terlambat']);

        if ($request->filled('status')) {
            if ($request->status === 'terlambat') {
                $query->where(function ($q) {
                    $q->where('status', 'terlambat')
                        ->orWhere(function ($lateQuery) {
                            $lateQuery->where('status', 'dipinjam')
                                ->whereDate('jatuh_tempo', '<', Carbon::today());
                        });
                });
            } elseif ($request->status === 'tepat') {
                $query->where('status', 'dipinjam')
                    ->whereDate('jatuh_tempo', '>=', Carbon::today());
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('detail.buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"));
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

        $loan = Peminjaman::with('detail.buku', 'user')
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
            $loan->status = $isTerlambat ? 'terlambat' : 'selesai';
            $loan->save();

            foreach ($loan->detail as $detail) {
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
