<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    /**
     * Kolom yang boleh diisi secara massal
     */
    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'jatuh_tempo',
        'tanggal_kembali',
        'status',
        'denda',
    ];

    /**
     * Cast tipe data kolom secara otomatis
     * Tanggal di-cast ke Carbon agar bisa pakai ->diffInDays() dll
     */
    protected function casts(): array
    {
        return [
            'tanggal_pinjam'  => 'date',
            'jatuh_tempo'     => 'date',
            'tanggal_kembali' => 'date',
            'denda'           => 'decimal:2',
        ];
    }

    /**
     * Relasi: peminjaman belongs to satu user (anggota)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: satu peminjaman bisa punya banyak detail buku
     * Dipakai Dev 4 (loan system) dan Dev 5 (pengembalian)
     */
    public function detail(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class);
    }
}
