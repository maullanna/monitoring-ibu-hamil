<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IotSensorData;
use App\Models\Pasien;
use App\Models\RekomendasiHidrasi;
use App\Models\JadwalNotifikasi;
use Carbon\Carbon;

class IotDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil pasien pertama
        $pasien = Pasien::first();
        
        if (!$pasien) {
            $this->command->info('Tidak ada pasien ditemukan. Jalankan AdminSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('Seeding IoT data untuk pasien: ' . $pasien->user->nama_lengkap);

        // Buat data IoT sensor untuk 7 hari terakhir
        $this->createIotSensorData($pasien);
        
        // Buat rekomendasi hidrasi
        $this->createHydrationRecommendations($pasien);
        
        // Buat jadwal notifikasi default
        $this->createNotificationSchedules($pasien);
        
        $this->command->info('IoT data seeding completed!');
    }

    private function createIotSensorData($pasien)
    {
        $deviceId = 'BOTOL_' . $pasien->id . '_001';
        
        // Data untuk 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Buat 3-5 data sensor per hari
            $recordsPerDay = rand(3, 5);
            
            for ($j = 0; $j < $recordsPerDay; $j++) {
                $hour = rand(6, 22); // Jam 6 pagi - 10 malam
                $minute = rand(0, 59);
                $timestamp = $date->copy()->setTime($hour, $minute);
                
                $volumeMl = rand(150, 300); // 150-300ml per tegukan
                $sensorType = ['flow_meter', 'weight_sensor', 'manual'][rand(0, 2)];
                $batteryLevel = rand(60, 100);
                $signalStrength = rand(70, 100);
                
                IotSensorData::create([
                    'pasien_id' => $pasien->id,
                    'device_id' => $deviceId,
                    'volume_ml' => $volumeMl,
                    'sensor_type' => $sensorType,
                    'timestamp' => $timestamp,
                    'battery_level' => $batteryLevel,
                    'signal_strength' => $signalStrength,
                ]);
            }
        }
        
        $this->command->info("Created " . (7 * rand(3, 5)) . " IoT sensor records");
    }

    private function createHydrationRecommendations($pasien)
    {
        // Buat rekomendasi untuk 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Hitung target dinamis
            $targetStandar = 2000;
            $faktorCuaca = rand(0, 300); // 0-300ml
            $faktorAktivitas = rand(0, 200); // 0-200ml
            $faktorBerat = rand(-100, 200); // -100 sampai 200ml
            $faktorTrimester = rand(100, 300); // 100-300ml
            
            $targetDinamis = $targetStandar + $faktorCuaca + $faktorAktivitas + $faktorBerat + $faktorTrimester;
            $targetDinamis = max(1500, $targetDinamis); // Minimal 1500ml
            
            $alasan = [];
            if ($faktorCuaca > 0) $alasan[] = "Cuaca panas - tambah {$faktorCuaca}ml";
            if ($faktorAktivitas > 0) $alasan[] = "Aktivitas tinggi - tambah {$faktorAktivitas}ml";
            if ($faktorBerat > 0) $alasan[] = "Berat badan tinggi - tambah {$faktorBerat}ml";
            if ($faktorBerat < 0) $alasan[] = "Berat badan rendah - kurangi " . abs($faktorBerat) . "ml";
            if ($faktorTrimester > 0) $alasan[] = "Trimester kehamilan - tambah {$faktorTrimester}ml";
            
            RekomendasiHidrasi::create([
                'pasien_id' => $pasien->id,
                'tanggal' => $date,
                'target_standar' => $targetStandar,
                'target_dinamis' => $targetDinamis,
                'berat_badan' => $pasien->berat_badan,
                'suhu_cuaca' => rand(25, 35),
                'aktivitas_fisik' => $pasien->aktivitas_fisik ?? 'sedang',
                'trimester_kehamilan' => $pasien->usia_kehamilan_minggu ? 
                    ($pasien->usia_kehamilan_minggu <= 12 ? 1 : ($pasien->usia_kehamilan_minggu <= 28 ? 2 : 3)) : 2,
                'faktor_cuaca' => $faktorCuaca,
                'faktor_aktivitas' => $faktorAktivitas,
                'faktor_berat' => $faktorBerat,
                'faktor_trimester' => $faktorTrimester,
                'alasan_rekomendasi' => implode(', ', $alasan),
            ]);
        }
        
        $this->command->info("Created 7 hydration recommendations");
    }

    private function createNotificationSchedules($pasien)
    {
        // Hapus jadwal lama jika ada
        JadwalNotifikasi::where('pasien_id', $pasien->id)->delete();
        
        $jadwalDefault = [
            [
                'waktu_notifikasi' => '08:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Selamat pagi! Jangan lupa minum air untuk memulai hari yang sehat.',
            ],
            [
                'waktu_notifikasi' => '10:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Waktunya minum air! Jaga kesehatan Anda dan bayi.',
            ],
            [
                'waktu_notifikasi' => '12:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Siang hari, jangan lupa minum air yang cukup!',
            ],
            [
                'waktu_notifikasi' => '15:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Sore hari, tetap jaga hidrasi tubuh Anda.',
            ],
            [
                'waktu_notifikasi' => '18:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Malam hari, pastikan target minum hari ini tercapai.',
            ],
            [
                'waktu_notifikasi' => '20:00:00',
                'jenis_trigger' => 'volume',
                'volume_threshold' => 1500,
                'pesan_notifikasi' => 'Target minum Anda belum tercapai. Segera minum air!',
            ],
        ];

        foreach ($jadwalDefault as $jadwal) {
            JadwalNotifikasi::create([
                'pasien_id' => $pasien->id,
                'waktu_notifikasi' => $jadwal['waktu_notifikasi'],
                'jenis_trigger' => $jadwal['jenis_trigger'],
                'volume_threshold' => $jadwal['volume_threshold'] ?? null,
                'pesan_notifikasi' => $jadwal['pesan_notifikasi'],
                'is_active' => true,
            ]);
        }
        
        $this->command->info("Created " . count($jadwalDefault) . " notification schedules");
    }
}
