@extends('layouts.admin')
@section('title', 'Kelola Kategori')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-1">
        <div class="moco-page-title">Kelola Kategori</div>
        <button class="btn btn-moco" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </button>
    </div>
    <div class="moco-page-sub">Atur kategori untuk mengelompokkan koleksi buku</div>

    {{-- Category Cards --}}
    {{--
    <div class="row g-3 mb-4">
        @foreach($categories as $cat)
        <div class="col-md-4">
            <div class="moco-card d-flex align-items-center gap-3 py-3">
                <div class="moco-cat-icon">
                    <i class="bi bi-folder2"></i>
                </div>
                <div class="flex-grow-1">
                    <div style="font-weight:600;font-size:14px;">{{ $cat->nama_kategori }}</div>
                    <div class="moco-note">{{ $cat->buku_count }} buku</div>
                </div>
                <button class="btn btn-sm btn-moco-outline me-1"
                    onclick="editKategori({{ $cat->id }}, '{{ $cat->nama_kategori }}', '{{ $cat->deskripsi }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}"
                    id="deleteCategoryFormOld-{{ $cat->id }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-moco-outline" type="button" style="color:var(--moco-text-faint);"
                            onclick="showConfirmModal('Hapus Kategori', 'Hapus kategori ini?', function() {
                                document.getElementById('deleteCategoryFormOld-{{ $cat->id }}').submit();
                            })">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    --}}

    {{-- Tabel --}}
    <div class="moco-eyebrow mb-2">Tabel Kategori</div>
    <div class="moco-card p-0" style="overflow:hidden;">
        <table class="table moco-table mb-0">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Buku</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td><strong>{{ $cat->nama_kategori }}</strong></td>
                        <td>{{ $cat->deskripsi ?: '-' }}</td>
                        <td>{{ $cat->buku_count }}</td>
                        <td>
                            <button class="btn btn-sm btn-moco-outline me-1"
                                onclick="editKategori({{ $cat->id }}, '{{ $cat->nama_kategori }}', '{{ $cat->deskripsi }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" class="d-inline"
                                id="deleteCategoryForm-{{ $cat->id }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-moco-outline" type="button" style="color:var(--moco-text-faint);"
                                        onclick="showConfirmModal('Hapus Kategori', 'Hapus kategori ini?', function() {
                                            document.getElementById('deleteCategoryForm-{{ $cat->id }}').submit();
                                        })">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center moco-note py-4">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:14px;border:1px solid var(--moco-line);">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-600">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="moco-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control moco-input"
                                placeholder="Nama kategori..." required>
                        </div>
                        <div class="mb-3">
                            <label class="moco-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control moco-input" rows="2"
                                placeholder="Deskripsi singkat..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-moco-outline" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-moco">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:14px;border:1px solid var(--moco-line);">
                <form method="POST" id="formEdit">
                    @csrf @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-600">Edit Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="moco-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="editNama" class="form-control moco-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="moco-label">Deskripsi</label>
                            <textarea name="deskripsi" id="editDeskripsi" class="form-control moco-input"
                                rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-moco-outline" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-moco">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function editKategori(id, nama, deskripsi) {
                document.getElementById('formEdit').action = `/admin/categories/${id}`;
                document.getElementById('editNama').value = nama;
                document.getElementById('editDeskripsi').value = deskripsi;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            }
        </script>
    @endpush
@endsection