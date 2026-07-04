<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Buku;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreLoanRequest;
use App\Services\LoanService;


class LoanController extends Controller
{
    /**
     * Display active loans for member
     */
    public function index()
    {
        $user = auth()->user();

        $aktivLoans = Peminjaman::where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->with('detail.buku')
            ->get();

        return view('member.loans.index', [
            'aktifLoans' => $aktivLoans,
            'aktif' => $aktivLoans->count(),
            'maxPinjam' => 3,
        ]);
    }

    /**
     * Show form to create new loan
     */
    public function create()
    {
        $user = auth()->user();

        $aktifCount = Peminjaman::where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->sum('peminjaman_detail.jumlah');

        // Fallback if relation data isn't ready
        if ($aktifCount === 0) {
            $aktifCount = Peminjaman::where('user_id', $user->id)
                ->where('status', 'dipinjam')
                ->with('detail')
                ->get()
                ->reduce(fn ($carry, $loan) => $carry + $loan->detail->sum('jumlah'), 0);
        }

        if ($aktifCount >= (int) config('library.max_buku_per_pinjam', 3)) {
            return back()->with('error', 'Anda sudah mencapai batas maksimal peminjaman');
        }



        $books = Buku::with('kategori')->get();

        return view('member.loans.create', compact('books'));

    }


    /**
     * Store new loan request (status: pending)
     */
    public function store(StoreLoanRequest $request)
    {
        $user = auth()->user();

        // cartItems already validated in request
        $cartItems = $request->validatedForStore()['cartItems'] ?? [];

        if (empty($cartItems)) {
            return back()->with('error', 'Keranjang peminjaman masih kosong.');
        }

        // Prevent duplicates: pending loan for same buku_id
        $pendingBukuIds = Peminjaman::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('detail')
            ->get()
            ->pluck('detail.*.buku_id')
            ->flatten()
            ->unique();

        foreach ($cartItems as $item) {
            $bukuId = (int) ($item['buku_id'] ?? 0);
            if ($bukuId && $pendingBukuIds->contains($bukuId)) {
                return back()->with('error', 'Anda sudah memiliki permintaan peminjaman untuk salah satu buku di keranjang Anda.');
            }
        }

        DB::transaction(function () use ($user, $cartItems, &$loan) {
            $loan = Peminjaman::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'tanggal_pinjam' => null,
                'jatuh_tempo' => null,
            ]);

            foreach ($cartItems as $item) {
                $jumlah = (int) $item['jumlah'];
                $bukuId = (int) $item['buku_id'];

                // Reduce stock safely (pre-check via request validation; this is a second line of defense)
                $affected = Buku::query()
                    ->where('id', $bukuId)
                    ->where('stok', '>=', $jumlah)
                    ->decrement('stok', $jumlah);

                if ($affected !== 1) {
                    throw new \RuntimeException('Stok buku tidak mencukupi untuk transaksi.');
                }

                PeminjamanDetail::create([
                    'peminjaman_id' => $loan->id,
                    'buku_id' => $bukuId,
                    'jumlah' => $jumlah,
                ]);
            }
        });


        // Clear cart after successful transaction
        $request->session()->forget('cart');

        return redirect()->route('member.loans.index')
            ->with('success', 'Permintaan peminjaman telah dikirim. Tunggu approval dari admin.');
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

        if ($loan->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        return view('member.loans.show', compact('loan'));
    }
}

