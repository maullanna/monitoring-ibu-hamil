<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notifikasi;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user ibu hamil
        $user = User::where('role', 'user')->first();
        
        if ($user) {
            // Notifikasi pengingat minum air
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Pengingat Minum Air',
                'pesan' => 'Jangan lupa minum air minimal 2000ml hari ini untuk menjaga kesehatan Anda dan bayi!',
                'tipe' => 'info',
                'prioritas' => 'normal',
                'action_url' => '/user/monitoring',
                'is_read' => false,
            ]);

            // Notifikasi tips kesehatan
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Tips Kesehatan Ibu Hamil',
                'pesan' => 'Minum air putih secara teratur dapat membantu mengurangi morning sickness dan menjaga cairan ketuban.',
                'tipe' => 'success',
                'prioritas' => 'low',
                'action_url' => null,
                'is_read' => false,
            ]);

            // Notifikasi urgent
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Jadwal Pemeriksaan',
                'pesan' => 'Jangan lupa jadwal pemeriksaan kehamilan minggu ini di dokter kandungan!',
                'tipe' => 'warning',
                'prioritas' => 'high',
                'action_url' => '/user/profil',
                'is_read' => false,
            ]);

            // Notifikasi dari admin
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Update Sistem',
                'pesan' => 'Sistem monitoring telah diperbarui dengan fitur notifikasi real-time. Silakan aktifkan notifikasi browser untuk pengalaman terbaik.',
                'tipe' => 'info',
                'prioritas' => 'normal',
                'action_url' => null,
                'is_read' => false,
            ]);
        }
    }
}
