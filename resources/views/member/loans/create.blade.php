@extends('layouts.app')
@section('title', 'Ajukan Peminjaman')

@section('content')
<a href="{{ route('member.loans.index') }}"
   class="d-inline-flex align-items-center gap-1 moco-note text-decoration-none mb-3"
   style="color:var(--moco-blue);">
    <i class="bi bi-arrow-left"></i> Kembali ke Peminjaman Saya
</a>

<div class="moco-page-title">Ajukan Peminjaman Buku</div>
<div class="moco-page-sub">Pilih buku yang ingin dipinjam. Admin akan mereviu dan menyetujui permintaan Anda.</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="moco-card">
            <form method="POST" action="{{ route('member.loans.store') }}">
                @csrf

                <div class="moco-eyebrow mb-3">Pilih Buku</div>

                <div class="mb-3">
                    <label class="form-label moco-label">Buku</label>
                    <select name="buku_id" class="form-select moco-input @error('buku_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Buku --</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('buku_id') == $book->id ? 'selected' : '' }}>
                                {{ $book->judul }} ({{ $book->penulis ?? 'Penulis tidak diketahui' }})
                            </option>
                        @endforeach
                    </select>
                    @error('buku_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label moco-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control moco-input @error('jumlah') is-invalid @enderror" 
                           value="{{ old('jumlah', 1) }}" min="1" max="3" required>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="moco-eyebrow mb-3 mt-4">Informasi Peminjaman</div>

                <div class="moco-alert moco-alert-info mb-3">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>Ketentuan Peminjaman:</strong><br>
                        • Periode peminjaman: <strong>7 hari</strong> sejak persetujuan admin<br>
                        • Denda keterlambatan: <strong>Rp 500</strong> per hari<br>
                        • Batas maksimal: <strong>3 buku</strong> per waktu
                    </div>
                </div>

                <button type="submit" class="btn btn-moco">
                    <i class="bi bi-check-lg"></i> Ajukan Peminjaman
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="moco-card">
            <div class="moco-eyebrow mb-3">Persyaratan</div>
            <div style="font-size:13.5px;line-height:1.8;">
                <p class="mb-2">
                    <i class="bi bi-check-circle" style="color:var(--moco-blue);"></i>
                    Akun aktif dan terdaftar
                </p>
                <p class="mb-2">
                    <i class="bi bi-check-circle" style="color:var(--moco-blue);"></i>
                    Tidak memiliki tunggakan denda
                </p>
                <p class="mb-2">
                    <i class="bi bi-check-circle" style="color:var(--moco-blue);"></i>
                    Kuota peminjaman masih tersedia
                </p>
                <p class="mb-0">
                    <i class="bi bi-clock-history" style="color:var(--moco-slate);"></i>
                    Menunggu persetujuan admin
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
