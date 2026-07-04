<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi secara massal (mass assignment)
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];

    /**
     * Kolom yang disembunyikan saat model di-serialize ke JSON/array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast tipe data kolom secara otomatis
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
<<<<<<< HEAD
     * Relasi: satu user bisa memiliki banyak peminjaman
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
=======
     * Get all loans for this user
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * Get all approvals made by this admin
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'approved_by');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->email === 'admin@library.com';
    }
>>>>>>> enter/feature/loan-system
}

