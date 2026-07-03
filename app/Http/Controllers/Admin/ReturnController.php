<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReturnController extends Controller
{
    /**
     * Display a listing of returns
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detail.buku'])
                        ->where('status', 'selesai');

        // Search filter
        if ($request->search) {
            $q = strtolower($request->search);
            $query->whereHas('user', function($q_user) use ($q) {
                $q_user->whereRaw("LOWER(name) LIKE ?", ["%{$q}%"]);
            })->orWhereHas('detail.buku', function($q_buku) use ($q) {
                $q_buku->whereRaw("LOWER(judul) LIKE ?", ["%{$q}%"]);
            });
        }

        // Status filter
        if ($request->status === 'terlambat') {
            $query->where('denda', '>', 0);
        } elseif ($request->status === 'tepat') {
            $query->where('denda', 0);
        }

        $returns = $query->orderBy('tanggal_kembali', 'desc')->paginate(10);

        return view('admin.returns.index', ['returns' => $returns]);
    }

    /**
     * Process return of a loan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'tanggal_kembali' => 'required|date'
        ]);

        $loan = Peminjaman::findOrFail($validated['peminjaman_id']);

        if ($loan->status !== 'dipinjam') {
            return back()->with('error', 'Hanya peminjaman aktif yang bisa dikembalikan');
        }

        $returnDate = Carbon::parse($validated['tanggal_kembali']);
        $dueDate = Carbon::parse($loan->jatuh_tempo);
        
        // Calculate fine: Rp 500 per day
        $denda = 0;
        if ($returnDate->gt($dueDate)) {
            $daysLate = $returnDate->diffInDays($dueDate);
            $denda = $daysLate * 500;
        }

        $loan->update([
            'status' => 'selesai',
            'tanggal_kembali' => $returnDate,
            'denda' => $denda,
        ]);

        if ($denda > 0) {
            return back()->with('success', "Pengembalian dicatat. Denda: Rp " . number_format($denda, 0, ',', '.'));
        } else {
            return back()->with('success', 'Pengembalian dicatat tanpa denda');
        }
    }
}
