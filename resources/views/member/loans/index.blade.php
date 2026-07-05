@extends('layouts.app')
@section('title', 'Peminjaman Saya')

@section('content')
    <div class="moco-page-title">Peminjaman Saya</div>
    <div class="moco-page-sub">Buku yang sedang kamu pinjam / ajukan saat ini</div>

    <div class="moco-alert moco-alert-info mb-4">
        <i class="bi bi-info-circle"></i>
        <div>
            Kuota aktif: <strong>{{ $aktif }} dari {{ $maxPinjam }} buku</strong> sedang dipinjam/diajukan
            &middot; sisa <strong>{{ max($maxPinjam - $aktif, 0) }}</strong> kuota tersedia
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
                <th>Detail</th>
            </tr>
            </thead>
            <tbody>
            @forelse($aktifLoans as $loan)
                @php
                    $isMenunggu = $loan->status === 'menunggu';
                    $daysLeft   = $loan->sisa_hari;
                    $isLate     = $loan->is_late;
                    $isNear     = $loan->is_near_due;
                @endphp
                <tr>
                    <td><strong>{{ $loan->detail->first()->buku->judul ?? '-' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->jatuh_tempo)->format('d M Y') }}</td>
                    <td>
                        @if($isMenunggu)
                            <span class="moco-note">-</span>
                        @elseif($isLate)
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
                        @if($isMenunggu)
                            <span class="badge-moco-active">
                                <i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi
                            </span>
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
                    <td>
                        <a href="{{ route('member.loans.show', $loan->id) }}"
                           class="btn btn-sm btn-moco-outline">
                            <i class="bi bi-eye"></i> Lihat
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center moco-note py-5">
                        Kamu belum meminjam / mengajukan buku apapun saat ini.<br>
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
