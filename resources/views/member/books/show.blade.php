@extends('layouts.app')
@section('title', $book->judul)

@section('content')
<a href="{{ route('member.books.index') }}"
   class="d-inline-flex align-items-center gap-1 moco-note text-decoration-none mb-3"
   style="color:var(--moco-blue);">
    <i class="bi bi-arrow-left"></i> Kembali ke Katalog
</a>

<div class="row g-4">
    {{-- Cover --}}
    <div class="col-md-3">
        <div class="moco-book-cover" style="aspect-ratio:3/4;height:auto;">
            @if($book->cover)
                <img src="{{ asset($book->cover) }}" alt="{{ $book->judul }}">
            @else
                <i class="bi bi-image fs-1" style="color:var(--moco-text-faint);"></i>
            @endif
        </div>
    </div>

    {{-- Info --}}
    <div class="col-md-9">
        <div class="moco-page-title mb-1">{{ $book->judul }}</div>
        <div class="moco-page-sub">{{ $book->penulis }} &middot; {{ $book->penerbit }}, {{ $book->tahun_terbit }}</div>

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <span class="badge bg-light text-dark border" style="font-size:12px;">ISBN {{ $book->isbn }}</span>
            <span class="badge bg-light text-dark border" style="font-size:12px;">{{ $book->kategori->nama_kategori ?? '-' }}</span>
            @if($book->tersedia > 0)
                <span class="badge-moco-stock">
                    Tersedia: {{ $book->tersedia }}
                </span>
            @else
                <span class="badge-moco-out">
                    Stok Habis
                </span>
            @endif
        </div>

        <div class="moco-eyebrow mb-1">Deskripsi</div>
        <p style="font-size:13.5px;color:var(--moco-text-soft);line-height:1.7;" class="mb-4">
            {{ $book->deskripsi }}
        </p>

        {{-- Form Cart (Tugas Dev 3) --}}
        @if($book->tersedia > 0)
            @if($kuotaAktif < $maxPinjam)
                <div class="moco-card">
                    <form method="POST" action="{{ route('member.cart.store') }}">
                        @csrf
                        <input type="hidden" name="buku_id" value="{{ $book->id }}">
                        <div class="row align-items-end g-3">
                            <div class="col-auto">
                                <label class="moco-label">Jumlah</label>
                                <input type="number" name="jumlah" value="1" min="1"
                                       max="{{ min($book->sisa_stok, $maxPinjam - $kuotaAktif) }}"
                                       class="form-control moco-input" style="width:90px;">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-moco">
                                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="moco-alert moco-alert-muted">
                    <strong>Batas peminjaman tercapai.</strong>
                </div>
            @endif
        @else
            <div class="moco-alert moco-alert-muted">
                <strong>Buku tidak tersedia.</strong>
            </div>
        @endif
    </div>
</div>
@endsection
