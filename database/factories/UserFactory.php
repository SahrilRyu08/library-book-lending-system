<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Data dummy untuk akun anggota
     * Semua password default 'password' untuk keperluan testing
     */
    public function definition(): array
    {
        return [
            'nama'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role'     => 'anggota',
        ];
    }
}
