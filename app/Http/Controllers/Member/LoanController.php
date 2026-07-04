<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Buku;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display active loans for member
     */
    public function index()
    {
        $user = auth()->user();
        
        // Count active loans
        $aktivLoans = Peminjaman::where('user_id', $user->id)
                            ->where('status', 'dipinjam')
                            ->with('detail.buku')
                            ->get();

        return view('member.loans.index', [
            'aktifLoans' => $aktivLoans,
            'aktif'      => $aktivLoans->count(),
            'maxPinjam'  => 3,
        ]);
    }

    /**
     * Show form to create new loan
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check active loans
        $aktifCount = Peminjaman::where('user_id', $user->id)
                            ->where('status', 'dipinjam')
                            ->count();

        if ($aktifCount >= 3) {
            return back()->with('error', 'Anda sudah mencapai batas maksimal peminjaman (3 buku)');
        }

        // Get available books
        $books = Buku::with('kategori')->get();

        return view('member.loans.create', compact('books'));
    }

    /**
     * Store new loan request (status: pending)
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        // Check if user already has pending loan request for this book
        $existingPending = Peminjaman::where('user_id', $user->id)
                                    ->where('status', 'pending')
                                    ->with('detail')
                                    ->get()
                                    ->pluck('detail.*.buku_id')
                                    ->flatten()
                                    ->contains($validated['buku_id']);

        if ($existingPending) {
            return back()->with('error', 'Anda sudah memiliki permintaan peminjaman untuk buku ini');
        }

        // Check active loans
        $aktivCount = Peminjaman::where('user_id', $user->id)
                            ->where('status', 'dipinjam')
                            ->count();

        if ($aktivCount >= 3) {
            return back()->with('error', 'Anda sudah mencapai batas maksimal peminjaman (3 buku)');
        }

        // Create loan request with status pending (will be approved by admin)
        $loan = Peminjaman::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'tanggal_pinjam' => null,
            'jatuh_tempo' => null,
        ]);

        // Add book details
        PeminjamanDetail::create([
            'peminjaman_id' => $loan->id,
            'buku_id' => $validated['buku_id'],
            'jumlah' => $validated['jumlah']
        ]);

        return redirect()->route('member.loans.index')->with('success', 'Permintaan peminjaman telah dikirim. Tunggu approval dari admin.');
    }

    /**
     * Display loan history (completed loans)
     */
    public function history()
    {
        $user = auth()->user();
        
        $history = Peminjaman::where('user_id', $user->id)
                            ->where('status', 'selesai')
                            ->with('detail.buku')
                            ->orderBy('tanggal_kembali', 'desc')
                            ->paginate(10);

        return view('member.loans.history', compact('history'));
    }

    /**
     * Show loan details
     */
    public function show($id)
    {
        $loan = Peminjaman::with(['user', 'detail.buku'])->findOrFail($id);

        // Check if user owns this loan
        if ($loan->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        return view('member.loans.show', compact('loan'));
    }
}