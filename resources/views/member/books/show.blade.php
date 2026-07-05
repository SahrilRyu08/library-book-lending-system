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
                <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->judul }}">
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
            @if((int)$book->stok > 0)
                <span class="badge-moco-stock">
                    Stok: {{ (int)$book->stok }}
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

        {{-- Quota Info --}}
        @if($book->stok > 0)
            @if($kuotaAktif < $maxPinjam)
                <div class="moco-alert moco-alert-info mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>
                        <strong>Kuota peminjaman Anda: {{ $kuotaAktif }} dari {{ $maxPinjam }} buku terpakai</strong>
                        <div class="moco-quota-bar mt-2">
                            <div class="fill" style="width:{{ ($kuotaAktif / $maxPinjam) * 100 }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="moco-card">
                    <form method="POST" action="{{ route('member.cart.store') }}">
                        @csrf
                        {{-- Jumlah hardcode 1, tidak perlu input --}}
                        <input type="hidden" name="buku_id" value="{{ $book->id }}">
                        <input type="hidden" name="jumlah" value="1">
                        <div class="d-flex align-items-center gap-3">
                            <div class="col">
                                <p class="moco-note mb-0">
                                    Sisa kuota: <strong style="color:var(--moco-blue);">
                                        {{ $maxPinjam - $kuotaAktif }} buku
                                    </strong>
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-moco">
                                    <i class="bi bi-basket-plus"></i> Tambah ke Keranjang
                                </button>
                                <a href="{{ route('member.cart.index') }}" class="btn btn-moco-outline">
                                    <i class="bi bi-basket"></i> Lihat Keranjang
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

            @else
                <div class="moco-alert moco-alert-muted mb-3">
                    <i class="bi bi-exclamation-circle"></i>
                    <div>
                        <strong>Maksimal peminjaman telah tercapai ({{ $kuotaAktif }} dari {{ $maxPinjam }} buku)</strong>
                        <div class="moco-quota-bar mt-2">
                            <div class="fill full" style="width:100%;"></div>
                        </div>
                        <div class="moco-note mt-1">
                            Kembalikan salah satu buku yang sedang dipinjam untuk bisa meminjam buku baru.
                        </div>
                    </div>
                </div>
                <div class="moco-card">
                    <div class="d-flex align-items-center gap-3">
                        <p class="moco-note mb-0">Anda sudah mencapai batas pinjam.</p>
                        <button class="btn btn-moco-disabled ms-auto" disabled>
                            <i class="bi bi-basket-plus"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            @endif

        @else
            <div class="moco-alert moco-alert-muted">
                <i class="bi bi-x-circle"></i>
                <div>Buku ini sedang tidak tersedia. Cek kembali nanti.</div>
            </div>
        @endif
    </div>
</div>
@endsection
