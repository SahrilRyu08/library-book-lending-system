<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanDetail extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_detail';

    /**
     * Kolom yang boleh diisi secara massal
     */
    protected $fillable = [
        'peminjaman_id',
        'buku_id',
        'jumlah',
    ];

    /**
     * Relasi: detail belongs to satu peminjaman (header)
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /**
     * Relasi: detail belongs to satu buku
     */
    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
