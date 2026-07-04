@extends('layouts.app')
@section('title', 'Peminjaman Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="moco-page-title">Peminjaman Saya</div>
        <div class="moco-page-sub">Buku yang sedang kamu pinjam saat ini</div>
    </div>
    @if($aktif < $maxPinjam)
    <a href="{{ route('member.loans.create') }}" class="btn btn-moco">
        <i class="bi bi-plus-lg"></i> Pinjam Buku
    </a>
    @endif
</div>

<div class="moco-alert moco-alert-info mb-4">
    <i class="bi bi-info-circle"></i>
    <div>
        Kuota aktif: <strong>{{ $aktif }} dari {{ $maxPinjam }} buku</strong> sedang dipinjam
        &middot; sisa <strong>{{ $maxPinjam - $aktif }}</strong> kuota tersedia
    </div>
</div>

@if($aktif > 0)
<div class="moco-card p-0" style="overflow:hidden;">
    <table class="table moco-table mb-0">
        <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Sisa Hari</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aktifLoans as $loan)
                @php
                    $daysLeft = \Carbon\Carbon::now()->diffInDays($loan->jatuh_tempo, false);
                    $isLate   = $daysLeft < 0;
                    $isNear   = $daysLeft >= 0 && $daysLeft <= 3;
                @endphp
                <tr>
                    <td><strong>{{ $loan->detail->first()->buku->judul ?? '-' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->jatuh_tempo)->format('d M Y') }}</td>
                    <td>
                        @if($isLate)
                            <span style="color:var(--moco-warn);font-weight:700;">
                                {{ abs((int)$daysLeft) }} hari telat
                            </span>
                        @else
                            <span style="color:var(--moco-blue);font-weight:600;">
                                {{ (int)$daysLeft }} hari lagi
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($isLate)
                            <span class="badge-moco-late">Terlambat</span>
                        @elseif($isNear)
                            <span class="badge-moco-late">
                                <i class="bi bi-bell"></i> H-{{ (int)$daysLeft }}
                            </span>
                        @else
                            <span class="badge-moco-active">Dipinjam</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="moco-card text-center py-5">
    <i class="bi bi-inbox" style="font-size:3rem;color:var(--moco-slate);opacity:0.3;"></i>
    <p class="moco-note mt-3 mb-0">
        Kamu belum meminjam buku apapun saat ini.<br>
        <a href="{{ route('member.books.index') }}" style="color:var(--moco-blue);">
            Jelajahi katalog →
        </a>
    </p>
</div>
@endif

<div class="mt-4">
    <a href="{{ route('member.loans.history') }}" class="moco-note text-decoration-none" style="color:var(--moco-blue);">
        Lihat riwayat peminjaman <i class="bi bi-arrow-right"></i>
    </a>
</div>

<p class="moco-note mt-3 mb-0">
    <strong>Catatan:</strong> Periode peminjaman adalah 7 hari sejak persetujuan admin. Keterlambatan pengembalian akan dikenakan denda Rp 500 per hari.
</p>
@endsection

