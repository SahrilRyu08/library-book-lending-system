@extends('layouts.app')
@section('title', 'Detail Peminjaman')

@section('content')
    <a href="{{ route('member.loans.index') }}"
       class="d-inline-flex align-items-center gap-1 moco-note text-decoration-none mb-3"
       style="color:var(--moco-blue);">
        <i class="bi bi-arrow-left"></i> Kembali ke Peminjaman Saya
    </a>

    <div class="moco-page-title">Detail Peminjaman</div>
    <div class="moco-page-sub">Informasi transaksi peminjaman kamu</div>

    @php
        $isMenunggu = $loan->status === 'menunggu';
        $isTerlambat = $loan->status === 'terlambat';
        $isDitolak = $loan->status === 'ditolak';
        $daysLeft   = $loan->sisa_hari;
        $isLate     = $loan->is_late;
        $isNear     = $loan->is_near_due;
    @endphp

    <div class="moco-card">
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
                    {{ \Carbon\Carbon::parse($loan->jatuh_tempo)->format('d M Y') }}
                </div>
            </div>
            <div class="col-6">
                <div class="moco-note mb-1">Status</div>
                @if($isMenunggu)
                    <span class="badge-moco-active">
                    <i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi Admin
                </span>
                @elseif($isDitolak)
                    <span class="badge bg-dark">Ditolak Admin</span>
                @elseif($isTerlambat || $isLate)
                    <span class="badge-moco-late">Terlambat {{ abs((int)$daysLeft) }} hari</span>
                @elseif($isNear)
                    <span class="badge-moco-late"><i class="bi bi-bell"></i> H-{{ (int)$daysLeft }}</span>
                @elseif($loan->tanggal_kembali !== null)
                    <span class="badge-moco-done">Selesai</span>
                @else
                    <span class="badge-moco-active">Dipinjam — {{ (int)$daysLeft }} hari lagi</span>
                @endif
            </div>
            <div class="col-6">
                <div class="moco-note mb-1">Estimasi Denda</div>
                <div style="font-weight:600;color:var(--moco-warn);">
                    Rp {{ number_format($estimasiDenda, 0, ',', '.') }}
                </div>
            </div>
        </div>

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
    </div>
@endsection
