@extends('layouts.admin')
@section('title', isset($book) ? 'Edit Buku' : 'Tambah Buku')

@section('content')
<a href="{{ route('admin.books.index') }}"
   class="d-inline-flex align-items-center gap-1 moco-note text-decoration-none mb-3"
   style="color:var(--moco-blue);">
    <i class="bi bi-arrow-left"></i> Kembali ke Kelola Buku
</a>

<div class="moco-page-title">{{ isset($book) ? 'Edit Buku' : 'Tambah Buku Baru' }}</div>

@if($errors->any())
    <div class="moco-alert moco-alert-warn mb-3">
        <i class="bi bi-exclamation-triangle"></i>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ isset($book) ? route('admin.books.update', $book->id) : route('admin.books.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if(isset($book)) @method('PUT') @endif

    <div class="row g-4">
        {{-- Upload Cover --}}
        <div class="col-md-3">
            <label class="moco-label mb-2">Sampul Buku</label>
            <div class="moco-upload-box" id="uploadBox" onclick="document.getElementById('coverInput').click()">
                @if(isset($book) && $book->cover)
                    <img src="{{ asset('storage/' . $book->cover) }}"
                         id="coverPreview"
                         style="max-height:200px;border-radius:8px;object-fit:cover;">
                @else
                    <i class="bi bi-cloud-arrow-up fs-3" style="color:var(--moco-blue);"></i>
                    <div style="font-size:13px;font-weight:600;" id="uploadLabel">Upload Sampul Buku</div>
                    <div class="moco-note">JPG/PNG, maks 2MB</div>
                @endif
            </div>
            <input type="file" id="coverInput" name="cover" accept="image/*" class="d-none">
        </div>

        {{-- Fields --}}
        <div class="col-md-9">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="moco-label">Judul Buku</label>
                    <input type="text" name="judul" value="{{ old('judul', $book->judul ?? '') }}"
                           class="form-control moco-input" placeholder="Judul buku..." required>
                </div>
                <div class="col-md-6">
                    <label class="moco-label">Penulis</label>
                    <input type="text" name="penulis" value="{{ old('penulis', $book->penulis ?? '') }}"
                           class="form-control moco-input" placeholder="Nama penulis..." required>
                </div>
                <div class="col-md-6">
                    <label class="moco-label">Penerbit</label>
                    <input type="text" name="penerbit" value="{{ old('penerbit', $book->penerbit ?? '') }}"
                           class="form-control moco-input" placeholder="Nama penerbit..." required>
                </div>
                <div class="col-md-3">
                    <label class="moco-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $book->tahun_terbit ?? date('Y')) }}"
                           class="form-control moco-input" min="1900" max="{{ date('Y') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="moco-label">Stok</label>
                    <input type="number" name="stok" value="{{ old('stok', $book->stok ?? 1) }}"
                           class="form-control moco-input" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="moco-label">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
                           class="form-control moco-input" placeholder="000-000-0000-00-0">
                </div>
                <div class="col-md-6">
                    <label class="moco-label">Kategori</label>
                    <select name="kategori_id" class="form-select moco-input" required>
                        <option value="">Pilih kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('kategori_id', $book->kategori_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="moco-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control moco-input" rows="4"
                              placeholder="Sinopsis singkat...">{{ old('deskripsi', $book->deskripsi ?? '') }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-moco px-4">
                    <i class="bi bi-check-lg"></i> Simpan Buku
                </button>
                <a href="{{ route('admin.books.index') }}" class="btn btn-moco-outline ms-2">Batal</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.getElementById('coverInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            const box = document.getElementById('uploadBox');
            box.innerHTML = `<img src="${ev.target.result}"
                style="max-height:200px;border-radius:8px;object-fit:cover;max-width:100%;">`;
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
@endsection
