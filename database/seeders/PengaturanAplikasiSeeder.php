<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaturanAplikasi;

class PengaturanAplikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        PengaturanAplikasi::truncate();
        
        // Buat pengaturan default
        PengaturanAplikasi::create([
            'nama_aplikasi' => 'Monitoring Ibu Hamil',
            'logo' => null, // Logo akan diupload manual oleh admin
            'deskripsi' => 'Sistem monitoring kesehatan ibu hamil dengan fitur IoT dan rekomendasi personal',
        ]);
        
        $this->command->info('Pengaturan aplikasi berhasil dibuat!');
    }
}
