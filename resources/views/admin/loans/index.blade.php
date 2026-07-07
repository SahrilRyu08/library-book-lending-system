@php use Carbon\Carbon; @endphp
@extends('layouts.admin')
@section('title', 'Peminjaman Aktif')

@section('content')
    <div class="moco-page-title">Peminjaman Aktif</div>
    <div class="moco-page-sub">Daftar buku yang sedang dipinjam / diajukan oleh anggota</div>

    <form method="GET" action="{{ route('admin.loans.index') }}" class="d-flex gap-2 mb-3">
        <div class="input-group" style="max-width:300px;">
        <span class="input-group-text bg-white" style="border-color:var(--moco-line);">
            <i class="bi bi-search"></i>
        </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control moco-input" placeholder="Cari anggota / buku...">
        </div>
        <select name="status" class="form-select moco-input" style="max-width:180px;">
            <option value="">Semua</option>
            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
            <option value="aman"     {{ request('status') == 'aman'     ? 'selected' : '' }}>Aman</option>
            <option value="mendekati"{{ request('status') == 'mendekati' ? 'selected' : '' }}>H-3</option>
            <option value="terlambat"{{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
        </select>
        <button class="btn btn-moco-outline" type="submit">Filter</button>
    </form>

    <div class="moco-card p-0" style="overflow:hidden;">
        <table class="table moco-table align-middle mb-0">
            <thead>
            <tr>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Sisa Hari</th>
                <th>Status</th>
                <th>Detail</th>
            </tr>
            </thead>
            <tbody>
            @forelse($loans as $loan)
                @php
                    $isMenunggu = $loan->status === 'menunggu';
                    $isDone = $loan->tanggal_kembali !== null;
                    $isLate = $loan->is_late;
                    $isNear = $loan->is_near_due;

                    $today = Carbon::today();
                    $dueDate = Carbon::parse($loan->jatuh_tempo)->startOfDay();

                    if ($today->gt($dueDate)) {
                        $daysLeft = $today->diffInDays($dueDate);
                        $isLate = true;
                    } else {
                        $daysLeft = $today->diffInDays($dueDate);
                        $isLate = false;
                    }

                    $hMinus = (int) config('library.notif_h_minus', 3);
                    $isNear = !$isLate && $daysLeft === $hMinus;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $loan->user->nama ?? '-' }}</div>
                        <div class="moco-note">{{ $loan->user->email ?? '' }}</div>
                    </td>
                    <td>
                        @if($loan->detail->isNotEmpty())
                            <div style="font-weight:600;">
                                {{ $loan->detail->first()->buku->judul ?? '-' }}
                            </div>
                            @if($loan->detail->count() > 1)
                                <div class="moco-note">
                                    +{{ $loan->detail->count() - 1 }} buku lainnya
                                </div>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->jatuh_tempo)->format('d M Y') }}</td>
                    <td>
                        @if($isMenunggu)
                            <span class="moco-note">-</span>
                        @elseif($isLate)
                            <span class="text-danger fw-bold">
                                Telat {{ $daysLeft }} hari
                             </span>
                        @else
                            <span class="text-primary fw-semibold">
                                {{ $daysLeft }} hari lagi
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($isMenunggu)

                            <span class="badge bg-secondary">
            Menunggu
        </span>

                        @elseif($isDone)

                            <span class="badge bg-success">
            Selesai
        </span>

                        @elseif($isLate)

                            <span class="badge-moco-late">
            Terlambat
        </span>

                        @elseif($isNear)

                            <span class="badge bg-warning text-dark">
            H-3
        </span>

                        @else

                            <span class="badge-moco-active">
            Dipinjam
        </span>

                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.loans.show', $loan->id) }}"
                           class="btn btn-sm btn-moco-outline">
                            <i class="bi bi-eye"></i> Lihat
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center moco-note py-4">
                        Tidak ada peminjaman aktif.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $loans->links() }}</div>
@endsection
