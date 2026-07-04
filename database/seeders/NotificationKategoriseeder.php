<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class NotificationKategoriseeder extends Seeder
{
    public function run(): void
    {
        $kategoris = ['Fiksi', 'Non-Fiksi', 'Teknologi', 'Sejarah', 'Sains'];

        foreach ($kategoris as $nama) {
            Kategori::updateOrCreate(['nama_kategori' => $nama]);
        }
    }
}
