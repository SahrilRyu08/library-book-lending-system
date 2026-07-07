<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $tanggal_pinjam
 * @property \Illuminate\Support\Carbon $jatuh_tempo
 * @property \Illuminate\Support\Carbon|null $tanggal_kembali
 * @property string $status
 * @property float $denda
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PeminjamanDetail[] $detail
 * @property-read int $sisa_hari
 * @property-read bool $is_late
 * @property-read bool $is_near_due
 */
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
    public function user(): BelongsTo
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

    /* ==========================================================
     | SCOPES
     |========================================================== */

    public function scopeMilikUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Peminjaman yang masih berjalan (belum dikembalikan)
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereNull('tanggal_kembali')
            ->whereIn('status', ['dipinjam', 'terlambat']);
    }

    /**
     * Peminjaman yang sudah selesai / dikembalikan
     */
    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }

    /* ==========================================================
     | ACCESSORS
     |========================================================== */

    /**
     * Selisih hari terhadap jatuh_tempo.
     * Positif = masih ada sisa hari, Negatif = sudah terlambat.
     * PENTING: dihitung dari jatuh_tempo, BUKAN tanggal_kembali.
     */
    public function getSisaHariAttribute(): int
    {
        if (!$this->jatuh_tempo) {
            return 0;
        }

        return Carbon::today()->diffInDays(
            $this->jatuh_tempo,
            false
        );
    }

    public function getIsLateAttribute(): bool
    {
        return $this->tanggal_kembali === null
            && $this->sisa_hari < 0;
    }

    public function getIsNearDueAttribute(): bool
    {
        return $this->tanggal_kembali === null
            && $this->sisa_hari >= 0
            && $this->sisa_hari <= config('library.notif_h_minus', 3);
    }
}
