<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    /**
     * Kolom yang boleh diisi secara massal
     */
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Relasi: satu kategori bisa memiliki banyak buku
     */
    public function buku()
    {
        return $this->hasMany(Buku::class);
    }
}
