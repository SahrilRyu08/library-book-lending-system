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
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Denda</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $loan)
                <tr>
                    <td>{{ $loan->user->nama ?? '-' }}</td>
                    <td>{{ $loan->details->first()->buku->judul ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}</td>
                    <td>
                        @if($loan->denda > 0)
                            <span class="badge-moco-late">Terlambat</span>
                        @else
                            <span class="badge-moco-done">Tepat Waktu</span>
                        @endif
                    </td>
                    <td>
                        @if($loan->denda > 0)
                            <span style="color:var(--moco-warn);font-weight:600;">
                                Rp {{ number_format($loan->denda, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="moco-note">Rp 0</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.returns.store') }}">
                            @csrf
                            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                            <button type="submit" class="btn btn-sm btn-moco"
                                    onclick="return confirm('Konfirmasi pengembalian buku ini?')">
                                Kembalikan
                            </button>
                        </form>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="text-center moco-note py-4">
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
