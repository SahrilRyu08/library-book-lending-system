<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    
    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'jatuh_tempo',
        'tanggal_kembali',
        'denda',
        'status',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'jatuh_tempo' => 'date',
        'tanggal_kembali' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the loan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the loan details
     */
    public function detail(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class, 'peminjaman_id');
    }

    /**
     * Calculate denda based on tanggal_kembali
     */
    public function calculateDenda(): int
    {
        if ($this->status !== 'selesai' || !$this->tanggal_kembali) {
            return 0;
        }

        $kembaliDate = Carbon::parse($this->tanggal_kembali);
        $tempoDate = Carbon::parse($this->jatuh_tempo);

        if ($kembaliDate->lte($tempoDate)) {
            return 0;
        }

        $hariTerlambat = $kembaliDate->diffInDays($tempoDate);
        return $hariTerlambat * 500; // Rp 500 per hari
    }

    /**
     * Get days remaining until jatuh_tempo
     */
    public function getDaysRemaining(): int
    {
        $now = Carbon::now();
        $tempo = Carbon::parse($this->jatuh_tempo);
        return (int)$now->diffInDays($tempo, false);
    }

    /**
     * Check if loan is overdue
     */
    public function isOverdue(): bool
    {
        return $this->getDaysRemaining() < 0;
    }

    /**
     * Check if loan is near due date (3 days or less)
     */
    public function isNearDueDate(): bool
    {
        $daysLeft = $this->getDaysRemaining();
        return $daysLeft >= 0 && $daysLeft <= 3;
    }

    /**
     * Scope: Filter pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Filter active loans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'dipinjam');
    }

    /**
     * Scope: Filter overdue loans
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'dipinjam')
                     ->whereDate('jatuh_tempo', '<', Carbon::now());
    }

    /**
     * Scope: Filter completed loans
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }
}
