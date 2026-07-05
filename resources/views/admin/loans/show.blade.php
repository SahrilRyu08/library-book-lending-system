@extends('layouts.admin')
@section('title', 'Detail Peminjaman')

@section('content')
<a href="{{ route('admin.loans.index') }}"
   class="d-inline-flex align-items-center gap-1 moco-note text-decoration-none mb-3"
   style="color:var(--moco-blue);">
    <i class="bi bi-arrow-left"></i> Kembali ke Peminjaman
</a>

<div class="moco-page-title">Detail Peminjaman</div>
<div class="moco-page-sub">Informasi lengkap transaksi peminjaman</div>

<div class="row g-4">

    {{-- Info Anggota --}}
    <div class="col-md-4">
        <div class="moco-card h-100">
            <div class="moco-eyebrow mb-3">Data Anggota</div>
            <div style="font-size:15px;font-weight:700;color:var(--moco-slate);">
                {{ $loan->user->nama ?? '-' }}
            </div>
            <div class="moco-note mb-3">{{ $loan->user->email ?? '-' }}</div>
            <div class="moco-eyebrow mb-2">Kuota Pinjam</div>
            <div style="font-size:13.5px;">
                Sedang meminjam <strong>{{ $loan->user->active_loans ?? 2 }}</strong>
                dari maks <strong>3</strong> buku
            </div>
            <div class="moco-quota-bar mt-2" style="max-width:100%;">
                <div class="fill" style="width:{{ (($loan->user->active_loans ?? 2) / 3) * 100 }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Info Peminjaman --}}
    <div class="col-md-8">
        <div class="moco-card">
            <div class="moco-eyebrow mb-3">Detail Transaksi</div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div class="moco-note mb-1">Tanggal Pinjam</div>
                    <div style="font-weight:600;">
                        {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="moco-note mb-1">Jatuh Tempo</div>
                    <div style="font-weight:600;">
                        {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="moco-note mb-1">Status</div>
                    @php
                        $daysLeft = \Carbon\Carbon::now()->diffInDays($loan->tanggal_kembali, false);
                        $isLate   = $daysLeft < 0;
                        $isNear   = $daysLeft >= 0 && $daysLeft <= 3;
                    @endphp
                    @if($isLate)
                        <span class="badge-moco-late">Terlambat {{ abs((int)$daysLeft) }} hari</span>
                    @elseif($isNear)
                        <span class="badge-moco-late"><i class="bi bi-bell"></i> Jatuh tempo H-{{ (int)$daysLeft }}</span>
                    @else
                        <span class="badge-moco-active">Dipinjam — {{ (int)$daysLeft }} hari lagi</span>
                    @endif
                </div>
                <div class="col-6">
                    <div class="moco-note mb-1">Estimasi Denda</div>
                    <div style="font-weight:600;color:var(--moco-warn);">
                        @if($isLate)
                            Rp {{ number_format(abs((int)$daysLeft) * 1000, 0, ',', '.') }}
                        @else
                            Rp 0
                        @endif
                    </div>
                </div>
            </div>

            {{-- Daftar Buku --}}
            <div class="moco-eyebrow mb-2">Buku yang Dipinjam</div>
            <div class="moco-card p-0" style="overflow:hidden;border-color:var(--moco-line);">
                <table class="table moco-table mb-0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->detail as $det)
                            <tr>
                                <td><strong>{{ $det->buku->judul ?? '-' }}</strong></td>
                                <td>{{ $det->buku->penulis ?? '-' }}</td>
                                <td>{{ $det->jumlah ?? 1 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tombol Kembalikan --}}
            @if(!$isDone)
                <div class="mt-3">
                    <form method="POST" action="{{ route('admin.returns.store') }}">
                        @csrf
                        <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                        <button type="submit" class="btn btn-moco"
                                onclick="return confirm('Proses pengembalian buku ini?')">
                            <i class="bi bi-arrow-return-left"></i> Proses Pengembalian
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
