@extends('layouts.admin')
@section('title', 'Laporan Peminjaman')

@section('content')
<div class="moco-page-title">Laporan Peminjaman</div>
<div class="moco-page-sub">Ringkasan transaksi berdasarkan periode</div>

{{-- Filter Form --}}
<form method="GET" action="{{ route('admin.reports.index') }}"
      class="d-flex gap-2 align-items-end mb-4">
    <div>
        <label class="moco-label">Dari</label>
        <input type="date" name="from" value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}"
               class="form-control moco-input">
    </div>
    <div>
        <label class="moco-label">Sampai</label>
        <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
               class="form-control moco-input">
    </div>
    <button type="submit" class="btn btn-moco">Terapkan</button>
</form>

{{-- Stat Summary --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="moco-stat-card">
            <div class="stat-num">{{ $totalTransaksi }}</div>
            <div class="stat-lbl">Total Transaksi</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="moco-stat-card">
            <div class="stat-num" style="color:var(--moco-warn);">
                Rp {{ number_format($totalDenda, 0, ',', '.') }}
            </div>
            <div class="stat-lbl">Total Denda</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="moco-stat-card">
            <div class="stat-num">{{ $jumlahTerlambat }}</div>
            <div class="stat-lbl">Keterlambatan</div>
        </div>
    </div>
</div>

{{-- Detail Tabel --}}
<div class="moco-eyebrow mb-2">Detail Transaksi</div>
<div class="moco-card p-0" style="overflow:hidden;">
    <table class="table moco-table mb-0">
        <thead>
            <tr>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
                <tr>
                    <td>{{ $loan->user->nama ?? '-' }}</td>
                    <td>{{ $loan->detail->first()->buku->judul ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>
                        {{ $loan->tanggal_kembali_aktual
                            ? \Carbon\Carbon::parse($loan->tanggal_kembali_aktual)->format('d M Y')
                            : '-' }}
                    </td>
                    <td>Rp {{ number_format($loan->denda, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center moco-note py-4">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $loans->withQueryString()->links() }}</div>
@endsection
