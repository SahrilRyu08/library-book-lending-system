<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanDetail extends Model
{
    protected $table = 'peminjaman_detail';

    protected $fillable = [
        'peminjaman_id',
        'buku_id',
        'jumlah',
    ];

    /**
     * Get the loan that owns this detail
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /**
     * Get the book
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }
}

