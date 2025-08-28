<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IotSensorData;
use App\Models\RekomendasiHidrasi;
use App\Models\JadwalNotifikasi;
use App\Models\Pasien;
use App\Models\Notifikasi;

class TestIotSystem extends Command
{
    protected $signature = 'iot:test {--pasien-id= : ID pasien untuk testing}';
    protected $description = 'Test IoT system dan tampilkan status data';

    public function handle()
    {
        $this->info('=== TESTING IOT SYSTEM ===');
        
        // Get pasien ID
        $pasienId = $this->option('pasien-id');
        if (!$pasienId) {
            $pasien = Pasien::first();
            if (!$pasien) {
                $this->error('Tidak ada pasien ditemukan!');
                return 1;
            }
            $pasienId = $pasien->id;
        }
        
        $pasien = Pasien::find($pasienId);
        if (!$pasien) {
            $this->error("Pasien dengan ID {$pasienId} tidak ditemukan!");
            return 1;
        }
        
        $this->info("Testing untuk pasien: {$pasien->user->nama_lengkap}");
        $this->line('');
        
        // Test IoT Sensor Data
        $this->testIotSensorData($pasien);
        
        // Test Rekomendasi Hidrasi
        $this->testRekomendasiHidrasi($pasien);
        
        // Test Jadwal Notifikasi
        $this->testJadwalNotifikasi($pasien);
        
        // Test API Endpoints
        $this->testApiEndpoints($pasien);
        
        $this->info('✅ IoT system test completed!');
        return 0;
    }
    
    private function testIotSensorData($pasien)
    {
        $this->info('📊 TESTING IOT SENSOR DATA:');
        
        $totalRecords = IotSensorData::where('pasien_id', $pasien->id)->count();
        $this->line("Total IoT records: {$totalRecords}");
        
        if ($totalRecords > 0) {
            $latestRecord = IotSensorData::where('pasien_id', $pasien->id)
                ->latest('timestamp')
                ->first();
                
            $this->line("Latest record:");
            $this->line("  - Device ID: {$latestRecord->device_id}");
            $this->line("  - Volume: {$latestRecord->volume_ml}ml");
            $this->line("  - Sensor Type: {$latestRecord->sensor_type}");
            $this->line("  - Timestamp: {$latestRecord->timestamp}");
            $this->line("  - Battery: {$latestRecord->battery_level}%");
            $this->line("  - Signal: {$latestRecord->signal_strength}%");
        }
        
        $this->line('');
    }
    
    private function testRekomendasiHidrasi($pasien)
    {
        $this->info('🎯 TESTING REKOMENDASI HIDRASI:');
        
        $rekomendasiHariIni = RekomendasiHidrasi::where('pasien_id', $pasien->id)
            ->whereDate('tanggal', today())
            ->first();
            
        if ($rekomendasiHariIni) {
            $this->line("Rekomendasi hari ini:");
            $this->line("  - Target Standar: {$rekomendasiHariIni->target_standar}ml");
            $this->line("  - Target Dinamis: {$rekomendasiHariIni->target_dinamis}ml");
            $this->line("  - Faktor Cuaca: +{$rekomendasiHariIni->faktor_cuaca}ml");
            $this->line("  - Faktor Aktivitas: +{$rekomendasiHariIni->faktor_aktivitas}ml");
            $this->line("  - Faktor Berat: " . ($rekomendasiHariIni->faktor_berat >= 0 ? '+' : '') . "{$rekomendasiHariIni->faktor_berat}ml");
            $this->line("  - Faktor Trimester: +{$rekomendasiHariIni->faktor_trimester}ml");
            $this->line("  - Alasan: {$rekomendasiHariIni->alasan_rekomendasi}");
        } else {
            $this->warn("Tidak ada rekomendasi untuk hari ini!");
        }
        
        $totalRekomendasi = RekomendasiHidrasi::where('pasien_id', $pasien->id)->count();
        $this->line("Total rekomendasi: {$totalRekomendasi}");
        
        $this->line('');
    }
    
    private function testJadwalNotifikasi($pasien)
    {
        $this->info('🔔 TESTING JADWAL NOTIFIKASI:');
        
        $jadwalAktif = JadwalNotifikasi::where('pasien_id', $pasien->id)
            ->where('is_active', true)
            ->get();
            
        $this->line("Jadwal aktif: {$jadwalAktif->count()}");
        
        foreach ($jadwalAktif as $jadwal) {
            $this->line("  - {$jadwal->waktu_notifikasi->format('H:i')} ({$jadwal->jenis_trigger})");
            if ($jadwal->volume_threshold) {
                $this->line("    Threshold: {$jadwal->volume_threshold}ml");
            }
            $this->line("    Pesan: " . substr($jadwal->pesan_notifikasi, 0, 50) . "...");
        }
        
        $this->line('');
    }
    
    private function testApiEndpoints($pasien)
    {
        $this->info('🌐 TESTING API ENDPOINTS:');
        
        // Test IoT real-time data
        $this->line("Testing IoT real-time data...");
        try {
            $iotData = IotSensorData::where('pasien_id', $pasien->id)
                ->today()
                ->orderBy('timestamp', 'desc')
                ->get();
                
            $totalVolumeHariIni = $iotData->sum('volume_ml');
            $this->line("  ✅ Total volume hari ini: {$totalVolumeHariIni}ml");
            $this->line("  ✅ Records hari ini: {$iotData->count()}");
            
        } catch (\Exception $e) {
            $this->error("  ❌ Error testing IoT data: " . $e->getMessage());
        }
        
        // Test rekomendasi statistics
        $this->line("Testing rekomendasi statistics...");
        try {
            $statistikMinggu = RekomendasiHidrasi::where('pasien_id', $pasien->id)
                ->whereBetween('tanggal', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->selectRaw('
                    AVG(target_dinamis) as rata_rata_target,
                    AVG(faktor_cuaca) as rata_rata_faktor_cuaca,
                    AVG(faktor_aktivitas) as rata_rata_faktor_aktivitas
                ')
                ->first();
                
            $this->line("  ✅ Rata-rata target minggu ini: " . round($statistikMinggu->rata_rata_target) . "ml");
            $this->line("  ✅ Rata-rata faktor cuaca: " . round($statistikMinggu->rata_rata_faktor_cuaca) . "ml");
            $this->line("  ✅ Rata-rata faktor aktivitas: " . round($statistikMinggu->rata_rata_faktor_aktivitas) . "ml");
            
        } catch (\Exception $e) {
            $this->error("  ❌ Error testing rekomendasi: " . $e->getMessage());
        }
        
        $this->line('');
    }
}
