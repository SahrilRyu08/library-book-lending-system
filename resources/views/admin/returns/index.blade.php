@php use Carbon\Carbon; @endphp
@extends('layouts.admin')
@section('title', 'Pengembalian')

@section('content')
    <div class="moco-page-title">Pengembalian Buku</div>
    <div class="moco-page-sub">Riwayat pengembalian dan rekap denda anggota</div>

    <form method="GET" action="{{ route('admin.returns.index') }}" class="d-flex gap-2 mb-3">
        <div class="input-group" style="max-width:300px;">
        <span class="input-group-text bg-white" style="border-color:var(--moco-line);">
            <i class="bi bi-search"></i>
        </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control moco-input" placeholder="Cari anggota / buku...">
        </div>
        <select name="status" class="form-select moco-input" style="max-width:180px;">
            <option value="">Semua Status</option>
            <option value="tepat" {{ request('status') == 'tepat' ? 'selected' : '' }}>Tepat Waktu</option>
            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
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
                <th>Status</th>
                <th>Denda</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($returns as $loan)
                <tr>
                    <td>{{ $loan->user->nama ?? '-' }}</td>
                    <td>{{ $loan->detail->first()->buku->judul ?? '-' }}</td>
                    <td>{{ Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>
                        @switch($loan->status)
                            @case('dipinjam')
                                <span class="badge bg-primary">Sedang Dipinjam</span>
                                @break
                            @case('terlambat')
                                <span class="badge-moco-late">Terlambat</span>
                                @break
                            @case('selesai')
                                @if($loan->denda > 0)
                                    <span class="badge bg-warning text-dark">Selesai Terlambat</span>
                                @else
                                    <span class="badge-moco-done">Tepat Waktu</span>
                                @endif
                                @break
                            @default
                                <span class="badge bg-secondary">-</span>
                        @endswitch
                    </td>
                    <td>
                        @if($loan->denda > 0)
                            <span class="text-danger fw-semibold">Rp {{ number_format($loan->denda, 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted"></span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.returns.store') }}" id="returnForm-{{ $loan->id }}">
                            @csrf
                            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                            <button type="button" class="btn btn-sm btn-moco"
                                    onclick="showConfirmModal('Konfirmasi Pengembalian', 'Konfirmasi pengembalian buku ini?', function () {
                                        document.getElementById('returnForm-{{ $loan->id }}').submit();
                                    })">
                                Kembalikan
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center moco-note py-4">
                        Tidak ada data pengembalian.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $returns->appends(request()->query())->links('components.pagination') }}
    </div>
    <p class="moco-note mt-2">
        * Denda dihitung otomatis: Rp 1.000 per hari keterlambatan.
    </p>
@endsection
