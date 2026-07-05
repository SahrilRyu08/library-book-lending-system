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
        return $this->hasMany(PeminjamanDetail::class, 'buku_id');
    }

    public function getTersediaAttribute(): int
    {
        $dipinjam = $this->detailPeminjaman()
            ->whereHas('peminjaman', function ($query) {
                $query->where('status', 'dipinjam');
            })
            ->sum('jumlah');

        return max(0, $this->stok - $dipinjam);
    }

    public function getSisaStokAttribute()
    {
        $stokTerpakai = \Illuminate\Support\Facades\DB::table('peminjaman_detail')
            ->join('peminjaman', 'peminjaman_detail.peminjaman_id', '=', 'peminjaman.id')
            ->where('peminjaman_detail.buku_id', $this->id)
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
            ->sum('peminjaman_detail.jumlah') ?? 0;

        return max(0, $this->stok - $stokTerpakai);
    }
}
