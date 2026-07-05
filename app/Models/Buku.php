<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

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

    protected $appends = ['tersedia'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(PeminjamanDetail::class);
    }

    public function getTersediaAttribute(): int
    {
        $dipinjam = $this->detailPeminjaman()
            ->whereHas('peminjaman', function ($query) {
                // Stok berkurang sejak status menunggu
                // bukan hanya saat sudah dikonfirmasi (dipinjam)
                $query->whereIn('status', ['menunggu', 'dipinjam']);
            })
            ->sum('jumlah');

        return max(0, $this->stok - $dipinjam);
    }
}
