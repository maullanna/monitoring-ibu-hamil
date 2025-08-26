<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PengaturanAplikasi;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create default app settings
        PengaturanAplikasi::create([
            'nama_aplikasi' => 'Monitoring Dehidrasi Ibu Hamil',
            'deskripsi' => 'Sistem monitoring asupan air minum untuk ibu hamil',
        ]);
    }
}
