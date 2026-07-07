@extends('layouts.app')
@section('title', 'Riwayat Peminjaman')

@section('content')
    <div class="moco-page-title">Riwayat Peminjaman</div>
    <div class="moco-page-sub">Semua buku yang kamu pinjam atau pernah pinjam</div>

    <div class="moco-card p-0" style="overflow:hidden;">
        <table class="table moco-table mb-0">
            <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Dikembalikan</th>
                <th>Denda</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse($historyLoans as $loan)
                @php
                    $isMenunggu = $loan->status === 'menunggu';
                    $isDipinjam = $loan->status === 'dipinjam';
                    $isTerlambat = $loan->status === 'terlambat';
                    $isSelesai = $loan->status === 'selesai';
                    $isDitolak = $loan->status === 'ditolak';
                @endphp
                <tr>
                    <td><strong>{{ $loan->detail->first()->buku->judul ?? '-' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>
                        @if($isDitolak)
                            Ditolak admin
                        @else
                            {{ $loan->tanggal_kembali
                                ? \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y')
                                : 'Belum dikembalikan' }}
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
                        @if($isMenunggu)
                            <span class="badge-moco-active">Menunggu Konfirmasi</span>
                        @elseif($isDitolak)
                            <span class="badge bg-dark">Ditolak</span>
                        @elseif($isDipinjam)
                            <span class="badge-moco-active">Dipinjam</span>
                        @elseif($isTerlambat)
                            <span class="badge-moco-late">Terlambat</span>
                        @elseif($isSelesai && $loan->denda > 0)
                            <span class="badge-moco-late">Dikembalikan Terlambat</span>
                        @else
                            <span class="badge-moco-done">Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center moco-note py-5">
                        Belum ada riwayat peminjaman.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $historyLoans->links() }}</div>
@endsection
