@extends('layouts.app')
@section('title', 'Peminjaman Saya')

@section('content')
<div class="moco-page-title">Peminjaman Saya</div>
<div class="moco-page-sub">Buku yang sedang kamu pinjam saat ini</div>

<div class="moco-alert moco-alert-info mb-4">
    <i class="bi bi-info-circle"></i>
    <div>
        Kuota aktif: <strong>{{ $aktif }} dari {{ $maxPinjam }} buku</strong> sedang dipinjam
        &middot; sisa <strong>{{ $maxPinjam - $aktif }}</strong> kuota tersedia
    </div>
</div>

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
            @forelse($aktifLoans as $loan)
                @php
                    $daysLeft = \Carbon\Carbon::now()->diffInDays($loan->tanggal_kembali, false);
                    $isLate   = $daysLeft < 0;
                    $isNear   = $daysLeft >= 0 && $daysLeft <= 3;
                @endphp
                <tr>
                    <td><strong>{{ $loan->detail->first()->buku->judul ?? '-' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}</td>
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
                        @if($loan->status == 'menunggu')
    <span class="badge-moco-late">Menunggu Konfirmasi Admin</span>
                        @elseif($isLate)
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
            @empty
                <tr>
                    <td colspan="5" class="text-center moco-note py-5">
                        Kamu belum meminjam buku apapun saat ini.<br>
                        <a href="{{ route('member.books.index') }}" style="color:var(--moco-blue);">
                            Jelajahi katalog →
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
