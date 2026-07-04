@extends('layouts.app')
@section('title', 'Riwayat Peminjaman')

@section('content')
<a href="{{ route('member.loans.index') }}"
   class="d-inline-flex align-items-center gap-1 moco-note text-decoration-none mb-3"
   style="color:var(--moco-blue);">
    <i class="bi bi-arrow-left"></i> Kembali ke Peminjaman Saya
</a>

<div class="moco-page-title">Riwayat Peminjaman</div>
<div class="moco-page-sub">Daftar buku yang telah dikembalikan</div>

<div class="moco-card p-0" style="overflow:hidden;">
    <table class="table moco-table align-middle mb-0">
        <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Denda</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $loan)
                @php
                    $denda = $loan->denda ?? 0;
                @endphp
                <tr>
                    <td><strong>{{ $loan->detail->first()->buku->judul ?? '-' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}</td>
                    <td>
                        @if($denda > 0)
                            <span style="color:var(--moco-warn);font-weight:600;">
                                Rp {{ number_format($denda, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="moco-note">Rp 0</span>
                        @endif
                    </td>
                    <td>
                        @if($denda > 0)
                            <span class="badge-moco-late">Terlambat</span>
                        @else
                            <span class="badge-moco-done">Tepat Waktu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center moco-note py-4">
                        Belum ada riwayat peminjaman.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $history->links() }}</div>
@endsection
