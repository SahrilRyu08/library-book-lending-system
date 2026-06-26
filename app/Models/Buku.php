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
        'cover',
        'stok'
    ];

    protected $appends = [
        'tersedia'
    ];

    public function kategori() {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailPeminjaman() {
        return $this -> hasMany(PeminjamanDetail::class);
    }

    public function tersedia() {
        $dipinjam = $this->detailPeminjaman()
            ->whereHas('peminjaman', function($query) {
                $query->where('status','dipinjam');
            })->sum('jumlah');

        return max(0, $this->stok -$dipinjam);

    }
}
