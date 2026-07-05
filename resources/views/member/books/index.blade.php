@extends('layouts.app')
@section('title', 'Katalog Buku')

@section('content')
<div class="mb-1"><span class="moco-eyebrow">Jelajahi Koleksi</span></div>
<div class="moco-page-title">Katalog Buku</div>
<div class="moco-page-sub">Temukan dan pinjam buku favoritmu</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('member.books.index') }}" class="d-flex gap-2 mb-4">
    <div class="input-group" style="max-width:360px;">
        <span class="input-group-text bg-white" style="border-color:var(--moco-line);">
            <i class="bi bi-search"></i>
        </span>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control moco-input" placeholder="Cari judul atau penulis...">
    </div>
    <select name="category" class="form-select moco-input" style="max-width:180px;">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                {{ $cat->nama_kategori }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-moco">Cari</button>
</form>

{{-- Book Grid --}}
<div class="row g-3">
    @forelse($books as $book)
        <div class="col-md-3 col-sm-4 col-6">
            {{-- Menggunakan sisa_stok untuk logika class is-out --}}
            <div class="moco-book-card {{ $book->sisa_stok <= 0 ? 'is-out' : '' }}">
                {{-- Cover --}}
                <div class="moco-book-cover">
                    @if($book->cover && file_exists(public_path($book->cover)))
                        <img src="{{ asset($book->cover) }}" alt="{{ $book->judul }}">
                    @else
                        <i class="bi bi-image fs-2"></i>
                    @endif
                </div>

                <div class="moco-book-title">{{ $book->judul }}</div>
                <div class="moco-book-author">{{ $book->penulis }}</div>

                <div class="moco-note mb-1" style="font-size:12px;">
                    {{ $book->kategori->nama_kategori ?? '-' }}
                </div>

                {{-- Bagian Status Stok --}}
                <div class="mt-auto">
                    @if($book->sisa_stok > 0)
                        <div class="mb-2">
                            <span class="badge-moco-stock">Tersedia: {{ $book->sisa_stok }}</span>
                        </div>
                        <a href="{{ route('member.books.show', $book->id) }}" class="btn btn-moco btn-sm w-100">Lihat Detail</a>
                    @else
                        <div class="mb-2">
                            <span class="badge-moco-stock" style="color: red; font-weight: bold;">Stok Habis</span>
                        </div>
                        <button class="btn btn-secondary btn-sm w-100" disabled>Tidak Tersedia</button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 moco-note">
            Buku tidak ditemukan.
        </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $books->withQueryString()->links() }}
</div>
@endsection
