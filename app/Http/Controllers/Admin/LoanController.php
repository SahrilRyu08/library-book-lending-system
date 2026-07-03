<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of loans (pending and active)
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detail.buku'])
                          ->whereIn('status', ['pending', 'dipinjam']);

        // Search filter
        if ($request->search) {
            $q = strtolower($request->search);
            $query->whereHas('user', function($q_user) use ($q) {
                $q_user->whereRaw("LOWER(name) LIKE ?", ["%{$q}%"])
                       ->orWhereRaw("LOWER(email) LIKE ?", ["%{$q}%"]);
            })->orWhereHas('detail.buku', function($q_buku) use ($q) {
                $q_buku->whereRaw("LOWER(judul) LIKE ?", ["%{$q}%"]);
            });
        }

        // Status filter
        if ($request->status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($request->status === 'active') {
            $query->where('status', 'dipinjam');
        } elseif ($request->status === 'terlambat') {
            $query->where('status', 'dipinjam')
                  ->whereDate('jatuh_tempo', '<', Carbon::now());
        } elseif ($request->status === 'mendekati') {
            $query->where('status', 'dipinjam')
                  ->whereDate('jatuh_tempo', '>=', Carbon::now())
                  ->whereDate('jatuh_tempo', '<=', Carbon::now()->addDays(3));
        } elseif ($request->status === 'aman') {
            $query->where('status', 'dipinjam')
                  ->whereDate('jatuh_tempo', '>', Carbon::now()->addDays(3));
        }

        $loans = $query->orderBy('jatuh_tempo', 'asc')->paginate(10);

        return view('admin.loans.index', ['loans' => $loans]);
    }

    /**
     * Display the specified loan
     */
    public function show($id)
    {
        $loan = Peminjaman::with(['user', 'detail.buku'])->findOrFail($id);
        $isDone = $loan->status === 'selesai';

        return view('admin.loans.show', compact('loan', 'isDone'));
    }

    /**
     * Approve a pending loan
     */
    public function approve($id)
    {
        $loan = Peminjaman::findOrFail($id);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman tidak bisa diapprove (status tidak sesuai)');
        }

        $loan->update([
            'status' => 'dipinjam',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'tanggal_pinjam' => Carbon::now(),
            'jatuh_tempo' => Carbon::now()->addDays(7),
        ]);

        return back()->with('success', 'Peminjaman telah diapprove. Periode peminjaman: 7 hari');
    }

    /**
     * Reject a pending loan
     */
    public function reject($id)
    {
        $loan = Peminjaman::findOrFail($id);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman tidak bisa ditolak (status tidak sesuai)');
        }

        $loan->delete();

        return back()->with('success', 'Permintaan peminjaman telah ditolak');
    }
}
