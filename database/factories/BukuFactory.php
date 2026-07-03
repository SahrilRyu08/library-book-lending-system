<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    /**
     * Data dummy untuk buku perpustakaan
     * kategori_id diambil random dari tabel kategori yang sudah ada
     * cover dikosongkan (nullable) karena upload foto ditangani Dev 2
     */
    public function definition(): array
    {
        return [
            'kategori_id'  => Kategori::inRandomOrder()->first()->id,
            'judul'        => fake()->sentence(3),
            'penulis'      => fake()->name(),
            'penerbit'     => fake()->company(),
            'tahun_terbit' => fake()->year(),
            'isbn'         => fake()->unique()->numerify('978-###-###-####-#'),
            'deskripsi'    => fake()->paragraph(),
            'cover'        => null,
            'stok'         => fake()->numberBetween(1, 10),
        ];
    }
}
