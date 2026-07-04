<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'buku';
    
>>>>>>> enter/feature/loan-system
    protected $fillable = [
        'kategori_id',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
<<<<<<< HEAD
        'deskripsi',
        'cover',
        'stok',
    ];

    protected $appends = ['tersedia'];

    public function kategori()
=======
        'stok'
    ];

    /**
     * Get the category
     */
    public function kategori(): BelongsTo
>>>>>>> enter/feature/loan-system
    {
        return $this->belongsTo(Kategori::class);
    }

<<<<<<< HEAD
    public function detailPeminjaman()
    {
        return $this->hasMany(PeminjamanDetail::class);
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
=======
    /**
     * Get all loan details for this book
     */
    public function peminjamanDetail(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class);
    }
>>>>>>> enter/feature/loan-system
}
