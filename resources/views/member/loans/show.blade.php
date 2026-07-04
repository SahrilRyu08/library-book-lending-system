@extends('layouts.app')
@section('title', 'Detail Peminjaman')

@section('content')
<a href="{{ route('member.loans.index') }}"
   class="d-inline-flex align-items-center gap-1 moco-note text-decoration-none mb-3"
   style="color:var(--moco-blue);">
    <i class="bi bi-arrow-left"></i> Kembali ke Peminjaman Saya
</a>

<div class="moco-page-title">Detail Peminjaman</div>
<div class="moco-page-sub">Informasi lengkap tentang peminjaman Anda</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="moco-card">
            <div class="moco-eyebrow mb-3">Status Peminjaman</div>
            
            @if($loan->status === 'pending')
                <div class="moco-alert moco-alert-warning mb-3">
                    <i class="bi bi-hourglass-split"></i>
                    <div>
                        Permintaan Anda sedang diproses oleh admin. Tunggu approval untuk mulai periode peminjaman.
                    </div>
                </div>
                <p class="moco-note">Tanggal Permintaan: {{ \Carbon\Carbon::parse($loan->created_at)->format('d M Y H:i') }}</p>
            @elseif($loan->status === 'dipinjam')
                @php
                    $daysLeft = \Carbon\Carbon::now()->diffInDays($loan->jatuh_tempo, false);
                    $isLate = $daysLeft < 0;
                @endphp
                <div class="moco-alert {{ $isLate ? 'moco-alert-warning' : 'moco-alert-info' }} mb-3">
                    <i class="bi {{ $isLate ? 'bi-exclamation-circle' : 'bi-info-circle' }}"></i>
                    <div>
                        @if($isLate)
                            <strong>Perhatian!</strong> Peminjaman Anda sudah terlambat {{ abs($daysLeft) }} hari.
                        @else
                            Periode peminjaman Anda tinggal <strong>{{ $daysLeft }} hari</strong> lagi.
                        @endif
                    </div>
                </div>
            @elseif($loan->status === 'selesai')
                <div class="moco-alert moco-alert-success mb-3">
                    <i class="bi bi-check-circle"></i>
                    <div>
                        Peminjaman telah selesai.
                        @if($loan->denda > 0)
                            Denda yang dikenakan: <strong>Rp {{ number_format($loan->denda, 0, ',', '.') }}</strong>
                        @else
                            Dikembalikan tepat waktu tanpa denda.
                        @endif
                    </div>
                </div>
            @endif

            <div class="row g-3">
                @if($loan->status !== 'pending')
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
                @endif
                @if($loan->status === 'selesai')
                <div class="col-6">
                    <div class="moco-note mb-1">Tanggal Kembali</div>
                    <div style="font-weight:600;">
                        {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d M Y') }}
                    </div>
                </div>
                <div class="col-6">
                    <div class="moco-note mb-1">Denda</div>
                    <div style="font-weight:600;color:var(--moco-warn);">
                        Rp {{ number_format($loan->denda, 0, ',', '.') }}
                    </div>
                </div>
                @endif
            </div>

            <div class="moco-eyebrow mb-2 mt-4">Buku yang Dipinjam</div>
            <div class="moco-card p-0" style="overflow:hidden;border-color:var(--moco-line);">
                <table class="table moco-table mb-0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->detail as $det)
                            <tr>
                                <td><strong>{{ $det->buku->judul ?? '-' }}</strong></td>
                                <td>{{ $det->buku->penulis ?? '-' }}</td>
                                <td>{{ $det->buku->kategori->nama ?? '-' }}</td>
                                <td>{{ $det->jumlah ?? 1 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="moco-card">
            <div class="moco-eyebrow mb-3">Informasi Penting</div>
            <div style="font-size:13.5px;line-height:1.8;">
                <p class="mb-2">
                    <i class="bi bi-calendar-event" style="color:var(--moco-blue);"></i>
                    <strong>Periode Peminjaman:</strong><br>
                    7 hari sejak persetujuan
                </p>
                <p class="mb-2">
                    <i class="bi bi-coin" style="color:var(--moco-warn);"></i>
                    <strong>Denda Keterlambatan:</strong><br>
                    Rp 500 per hari
                </p>
                <p class="mb-0">
                    <i class="bi bi-question-circle" style="color:var(--moco-slate);"></i>
                    <strong>Pertanyaan?</strong><br>
                    Hubungi petugas perpustakaan kami untuk bantuan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
