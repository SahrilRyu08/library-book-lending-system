<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'buku';
    
    protected $fillable = [
        'kategori_id',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'stok'
    ];

    /**
     * Get the category
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Get all loan details for this book
     */
    public function peminjamanDetail(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class);
    }
}
