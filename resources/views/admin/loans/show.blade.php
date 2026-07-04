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
                {{ $loan->user->name ?? '-' }}
            </div>
            <div class="moco-note mb-3">{{ $loan->user->email ?? '-' }}</div>
            <div class="moco-eyebrow mb-2">Kuota Pinjam</div>
            @php
                $aktivLoans = $loan->user->peminjaman()->where('status', 'dipinjam')->count();
            @endphp
            <div style="font-size:13.5px;">
                Sedang meminjam <strong>{{ $aktivLoans }}</strong>
                dari maks <strong>3</strong> buku
            </div>
            <div class="moco-quota-bar mt-2" style="max-width:100%;">
                <div class="fill" style="width:{{ ($aktivLoans / 3) * 100 }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Info Peminjaman --}}
    <div class="col-md-8">
        <div class="moco-card">
            <div class="moco-eyebrow mb-3">Detail Transaksi</div>
            
            {{-- Status Badge --}}
            <div class="mb-3">
                @if($loan->status === 'pending')
                    <span class="badge bg-warning">⏳ Menunggu Approval</span>
                @elseif($loan->status === 'dipinjam')
                    @php
                        $daysLeft = \Carbon\Carbon::now()->diffInDays($loan->jatuh_tempo, false);
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
                @elseif($loan->status === 'selesai')
                    <span class="badge-moco-done">Selesai</span>
                @endif
            </div>

            @if($loan->status === 'pending')
                {{-- Pending Info --}}
                <div class="moco-alert moco-alert-warning mb-3">
                    <i class="bi bi-exclamation-circle"></i>
                    <div>
                        Peminjaman ini menunggu persetujuan dari Anda. Klik tombol di bawah untuk menyetujui atau menolak permintaan.
                    </div>
                </div>
            @endif

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div class="moco-note mb-1">Tanggal Permintaan</div>
                    <div style="font-weight:600;">
                        {{ \Carbon\Carbon::parse($loan->created_at)->format('d M Y H:i') }}
                    </div>
                </div>
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
                @endif
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

            {{-- Action Buttons --}}
            <div class="mt-3 d-flex gap-2">
                @if($loan->status === 'pending')
                    {{-- Approve Button --}}
                    <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg"></i> Setujui Peminjaman
                        </button>
                    </form>

                    {{-- Reject Button --}}
                    <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}" class="flex-grow-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('Tolak permintaan peminjaman ini?')">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                    </form>
                @elseif($loan->status === 'dipinjam')
                    {{-- Return Button --}}
                    <button type="button" class="btn btn-moco" data-bs-toggle="modal" data-bs-target="#returnModal">
                        <i class="bi bi-arrow-return-left"></i> Proses Pengembalian
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Return Modal --}}
@if($loan->status === 'dipinjam')
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Pengembalian Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.returns.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="peminjaman_id" value="{{ $loan->id }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" class="form-control" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>

                    @php
                        $tempoDate = \Carbon\Carbon::parse($loan->jatuh_tempo);
                        $today = \Carbon\Carbon::now();
                        $daysLate = $today->diffInDays($tempoDate);
                        $estimatedDenda = $daysLate > 0 ? 0 : abs($daysLate) * 500;
                    @endphp
                    
                    @if($estimatedDenda > 0)
                    <div class="moco-alert moco-alert-warning">
                        <i class="bi bi-exclamation-circle"></i>
                        <div>
                            <strong>Estimasi Denda:</strong> Rp {{ number_format($estimatedDenda, 0, ',', '.') }}
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-moco">Konfirmasi Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
