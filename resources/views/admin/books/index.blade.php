@extends('layouts.admin')
@section('title', 'Kelola Buku')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-1">
    <div class="moco-page-title">Kelola Buku</div>
    <a href="{{ route('admin.books.create') }}" class="btn btn-moco">
        <i class="bi bi-plus-lg"></i> Tambah Buku
    </a>
</div>
<div class="moco-page-sub">Daftar semua buku dalam sistem</div>

<form method="GET" action="{{ route('admin.books.index') }}" class="d-flex gap-2 mb-3">
    <div class="input-group" style="max-width:320px;">
        <span class="input-group-text bg-white" style="border-color:var(--moco-line);">
            <i class="bi bi-search"></i>
        </span>
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control moco-input" placeholder="Cari buku...">
    </div>
    <button class="btn btn-moco-outline" type="submit">Cari</button>
</form>

<div class="moco-card p-0" style="overflow:hidden;">
    <table class="table moco-table align-middle mb-0">
        <thead>
            <tr>
                <th>Cover</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($books as $book)
                <tr>
                    <td>
                        <div style="width:40px;height:52px;border-radius:6px;background:var(--moco-muted-bg);overflow:hidden;">
                            @if($book->cover)
                                <img src="{{ asset('storage/' . $book->cover) }}"
                                     style="width:100%;height:100%;object-fit:cover;" alt="">
                            @endif
                        </div>
                    </td>
                    <td><strong>{{ $book->judul }}</strong></td>
                    <td>{{ $book->penulis }}</td>
                    <td>{{ $book->kategori->nama_kategori ?? '-' }}</td>
                    <td>
                        @if($book->stok > 0)
                            <span class="badge-moco-stock">{{ $book->stok }}</span>
                        @else
                            <span class="badge-moco-out">0</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.books.edit', $book->id) }}"
                           class="btn btn-sm btn-moco-outline me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.books.destroy', $book->id) }}"
                              class="d-inline"
                              onsubmit="return confirm('Hapus buku ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-moco-outline" type="submit"
                                    style="color:var(--moco-text-faint);">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center moco-note py-4">Belum ada buku.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $books->withQueryString()->links() }}</div>
@endsection
