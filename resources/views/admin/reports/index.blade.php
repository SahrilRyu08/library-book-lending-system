@extends('layouts.admin')

@section('title', 'Laporan Peminjaman')

@section('content')

    <div class="moco-page-title">
        Laporan Peminjaman
    </div>

    <div class="moco-page-sub mb-4">
        Ringkasan transaksi berdasarkan periode
    </div>

    <form method="GET"
          action="{{ route('admin.reports.index') }}"
          class="row g-3 align-items-end mb-4">

        <div class="col-md-3">
            <label class="moco-label">Dari</label>

            <input
                type="date"
                name="start_date"
                class="form-control moco-input"
                value="{{ request('start_date') }}">
        </div>

        <div class="col-md-3">
            <label class="moco-label">Sampai</label>

            <input
                type="date"
                name="end_date"
                class="form-control moco-input"
                value="{{ request('end_date') }}">
        </div>

        <div class="col-md-auto">
            <button class="btn btn-moco">
                Terapkan
            </button>
        </div>

    </form>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="moco-stat-card">

                <div class="stat-num">
                    {{ $totalTransaksi }}
                </div>

                <div class="stat-lbl">
                    Total Transaksi
                </div>

            </div>
        </div>

        <div class="col-md-4">
            <div class="moco-stat-card">
                <div class="stat-num text-warning">
                    Rp {{ number_format($totalDenda,0,',','.') }}
                </div>
                <div class="stat-lbl">
                    Total Denda
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="moco-stat-card">
                <div class="stat-num">
                    {{ $jumlahTerlambat }}
                </div>
                <div class="stat-lbl">
                    Terlambat Aktif
                </div>
            </div>
        </div>

    </div>

    <div class="moco-card p-0">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
                <th>Denda</th>
            </tr>
            </thead>
            <tbody>
            @forelse($loans as $loan)
                <tr>
                    <td>
                        {{ $loan->user?->nama ?? '-' }}
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
                    <td>
                        {{ optional($loan->tanggal_pinjam)->format('d M Y') }}
                    </td>
                    <td>
                        {{ optional($loan->tanggal_kembali)->format('d M Y') ?? '-' }}
                    </td>
                    <td>
                        @switch($loan->status)
                            @case('menunggu')
                                <span class="badge bg-secondary">
                                Menunggu
                            </span>
                                @break
                            @case('dipinjam')
                                <span class="badge bg-primary">
                                Dipinjam
                            </span>
                                @break
                            @case('terlambat')
                                <span class="badge bg-danger">
                                Terlambat
                            </span>
                                @break
                            @case('ditolak')
                                <span class="badge bg-dark">
                                Ditolak
                            </span>
                                @break
                            @default
                                <span class="badge bg-success">
                                Selesai
                            </span>
                        @endswitch
                    </td>
                    <td>
                        Rp {{ number_format($loan->denda ?? 0,0,',','.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        Tidak ada data.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $loans->appends(request()->query())->links('components.pagination') }}
    </div>
@endsection
