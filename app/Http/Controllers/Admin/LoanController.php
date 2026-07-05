<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detail.buku'])
            ->whereNull('tanggal_kembali');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('detail.buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"));
            });
        }

        $today = Carbon::today();

        if ($request->status === 'menunggu') {
            $query->where('status', 'menunggu');
        } elseif ($request->status === 'terlambat') {
            $query->whereIn('status', ['dipinjam', 'terlambat'])
                ->whereDate('jatuh_tempo', '<', $today);
        } elseif ($request->status === 'mendekati') {
            $query->where('status', 'dipinjam')
                ->whereBetween(
                    'jatuh_tempo',
                    [
                        $today,
                        Carbon::today()->addDays(3)
                    ]
                );
        } elseif ($request->status === 'aman') {
            $query->where('status', 'dipinjam')
                ->whereDate(
                    'jatuh_tempo',
                    '>',
                    Carbon::today()->addDays(3)
                );
        }

        $loans = $query->orderBy('jatuh_tempo')->paginate(10)->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    public function show($id)
    {
        $loan = Peminjaman::with(['user', 'detail.buku'])->findOrFail($id);

        $isDone = $loan->tanggal_kembali !== null;

        return view('admin.loans.show', compact('loan', 'isDone'));
    }

    public function confirm($id)
    {
        $loan = Peminjaman::with(['user', 'detail.buku'])->findOrFail($id);
        $loan->update([
            'status' => 'dipinjam',
            'tanggal_pinjam' => now(),
            'jatuh_tempo' => now()->addDays(config('library.max_hari_pinjam', 7)),
        ]);

        if ($loan->user) {
            $loan->user->notify(new \App\Notifications\PeminjamanDikonfirmasiNotification($loan));
        }

        return back()->with('success', 'Peminjaman berhasil dikonfirmasi.');
    }
}
