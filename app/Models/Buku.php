<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

    /**
     * Kolom yang boleh diisi secara massal
     */
    protected $fillable = [
        'kategori_id',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'deskripsi',
        'cover',
        'stok',
    ];

    /**
     * Tambahkan accessor 'tersedia' ke output model secara otomatis
     * Dipakai di katalog member dan validasi peminjaman (Dev 3 & 4)
     */
    protected $appends = ['tersedia'];

    /**
     * Relasi: buku belongs to satu kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi: satu buku bisa ada di banyak peminjaman_detail
     */
    public function detailPeminjaman()
    {
        return $this->hasMany(PeminjamanDetail::class);
    }

    /**
     * Accessor: hitung stok yang benar-benar tersedia
     * Rumus: stok total - jumlah yang sedang berstatus 'dipinjam'
     * Digunakan oleh Dev 3 (tampil di katalog) dan Dev 4 (cegah pinjam kalau habis)
     */
    public function getTersediaAttribute(): int
    {
        $dipinjam = $this->detailPeminjaman()
            ->whereHas('peminjaman', function ($query) {
                $query->where('status', 'dipinjam');
            })
            ->sum('jumlah');

        return max(0, $this->stok - $dipinjam);
    }
}
