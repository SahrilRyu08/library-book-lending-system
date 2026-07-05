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
        $query = Peminjaman::with(['user', 'detail.buku']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('detail.buku', fn ($b) => $b->where('judul', 'like', "%{$search}%"));
            });
        }

        $today = Carbon::now()->toDateString();

        if ($request->status === 'terlambat') {
            $query->where(function ($q) use ($today) {
                $q->where('status', 'terlambat')
                    ->orWhere(function ($q2) use ($today) {
                        $q2->where('status', 'dipinjam')->whereDate('jatuh_tempo', '<', $today);
                    });
            });
        } elseif ($request->status === 'mendekati') {
            $query->where('status', 'dipinjam')
                ->whereBetween('jatuh_tempo', [$today, Carbon::now()->addDays(3)->toDateString()]);
        } elseif ($request->status === 'aman') {
            $query->where('status', 'dipinjam')
                ->whereDate('jatuh_tempo', '>', Carbon::now()->addDays(3)->toDateString());
        }

        $loans = $query->orderBy('jatuh_tempo')->paginate(10)->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    public function show($id)
    {
        $loan = Peminjaman::with(['user', 'detail.buku'])->findOrFail($id);

        $isDone = $loan->status === 'selesai';

        return view('admin.loans.show', compact('loan', 'isDone'));
    }
}
