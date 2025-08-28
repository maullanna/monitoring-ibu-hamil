<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Pasien;
use App\Models\IotSensorData;
use App\Models\MonitoringDehidrasi;
use App\Models\RekomendasiHidrasi;
use App\Models\JadwalNotifikasi;
use Illuminate\Support\Facades\Http;

class TestApiSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test semua API endpoint untuk IoT dan Mobile App';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 TESTING API SYSTEM UNTUK IOT & MOBILE APP');
        $this->line('================================================');
        
        // Test data availability
        $this->testDataAvailability();
        
        // Test API endpoints
        $this->testApiEndpoints();
        
        // Test IoT integration
        $this->testIotIntegration();
        
        // Test mobile app features
        $this->testMobileAppFeatures();
        
        $this->info('✅ API System Test selesai!');
        return 0;
    }
    
    private function testDataAvailability()
    {
        $this->info('📊 TESTING DATA AVAILABILITY:');
        
        $totalUsers = User::where('role', 'user')->count();
        $totalPasien = Pasien::count();
        $totalIotData = IotSensorData::count();
        $totalMonitoring = MonitoringDehidrasi::count();
        $totalRekomendasi = RekomendasiHidrasi::count();
        $totalJadwal = JadwalNotifikasi::count();
        
        $this->line("  ✅ Total Users (Ibu Hamil): {$totalUsers}");
        $this->line("  ✅ Total Pasien: {$totalPasien}");
        $this->line("  ✅ Total IoT Data: {$totalIotData}");
        $this->line("  ✅ Total Monitoring: {$totalMonitoring}");
        $this->line("  ✅ Total Rekomendasi: {$totalRekomendasi}");
        $this->line("  ✅ Total Jadwal Notifikasi: {$totalJadwal}");
        
        if ($totalUsers == 0) {
            $this->warn("  ⚠️  Tidak ada user ibu hamil - jalankan seeder dulu!");
        }
        
        $this->line('');
    }
    
    private function testApiEndpoints()
    {
        $this->info('🌐 TESTING API ENDPOINTS:');
        
        $baseUrl = config('app.url');
        
        // Test IoT endpoints
        $this->line('  🔌 Testing IoT Endpoints:');
        
        // Test sensor data endpoint
        try {
            $response = Http::post("{$baseUrl}/api/iot/sensor-data", [
                'pasien_id' => 1,
                'device_id' => 'TEST_DEVICE_001',
                'volume_ml' => 250,
                'sensor_type' => 'flow_meter',
                'timestamp' => now()->toISOString(),
                'battery_level' => 85,
                'signal_strength' => 90,
            ]);
            
            if ($response->successful()) {
                $this->line("    ✅ POST /api/iot/sensor-data - SUCCESS");
            } else {
                $this->warn("    ⚠️  POST /api/iot/sensor-data - FAILED: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("    ❌ POST /api/iot/sensor-data - ERROR: " . $e->getMessage());
        }
        
        // Test real-time data endpoint
        try {
            $response = Http::get("{$baseUrl}/api/iot/real-time-data", [
                'pasien_id' => 1
            ]);
            
            if ($response->successful()) {
                $this->line("    ✅ GET /api/iot/real-time-data - SUCCESS");
            } else {
                $this->warn("    ⚠️  GET /api/iot/real-time-data - FAILED: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("    ❌ GET /api/iot/real-time-data - ERROR: " . $e->getMessage());
        }
        
        // Test statistics endpoint
        try {
            $response = Http::get("{$baseUrl}/api/iot/statistics", [
                'pasien_id' => 1,
                'periode' => 'week'
            ]);
            
            if ($response->successful()) {
                $this->line("    ✅ GET /api/iot/statistics - SUCCESS");
            } else {
                $this->warn("    ⚠️  GET /api/iot/statistics - FAILED: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("    ❌ GET /api/iot/statistics - ERROR: " . $e->getMessage());
        }
        
        // Test legacy endpoint
        try {
            $response = Http::post("{$baseUrl}/api/iot/monitoring", [
                'device_id' => 'TEST_DEVICE_001',
                'pasien_id' => 1,
                'jumlah_minum_ml' => 300,
                'timestamp' => now()->toISOString(),
            ]);
            
            if ($response->successful()) {
                $this->line("    ✅ POST /api/iot/monitoring (legacy) - SUCCESS");
            } else {
                $this->warn("    ⚠️  POST /api/iot/monitoring (legacy) - FAILED: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("    ❌ POST /api/iot/monitoring (legacy) - ERROR: " . $e->getMessage());
        }
        
        // Test chart endpoint
        try {
            $response = Http::get("{$baseUrl}/api/iot/monitoring/1/chart");
            
            if ($response->successful()) {
                $this->line("    ✅ GET /api/iot/monitoring/{id}/chart - SUCCESS");
            } else {
                $this->warn("    ⚠️  GET /api/iot/monitoring/{id}/chart - FAILED: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("    ❌ GET /api/iot/monitoring/{id}/chart - ERROR: " . $e->getMessage());
        }
        
        $this->line('');
    }
    
    private function testIotIntegration()
    {
        $this->info('🔌 TESTING IOT INTEGRATION:');
        
        // Test IoT data flow
        $pasien = Pasien::first();
        if (!$pasien) {
            $this->warn("  ⚠️  Tidak ada pasien untuk test IoT");
            return;
        }
        
        $this->line("  📱 Testing dengan Pasien: {$pasien->user->nama_lengkap}");
        
        // Test sensor data creation
        try {
            $iotData = IotSensorData::create([
                'pasien_id' => $pasien->id,
                'device_id' => 'TEST_DEVICE_' . time(),
                'volume_ml' => rand(150, 300),
                'sensor_type' => 'flow_meter',
                'timestamp' => now(),
                'battery_level' => rand(60, 100),
                'signal_strength' => rand(70, 100),
            ]);
            
            $this->line("    ✅ IoT Sensor Data created: ID {$iotData->id}");
            
            // Test monitoring integration
            $monitoring = MonitoringDehidrasi::where('pasien_id', $pasien->id)
                ->whereDate('tanggal', today())
                ->first();
                
            if ($monitoring) {
                $this->line("    ✅ Monitoring data integrated: {$monitoring->jumlah_minum_ml}ml");
            } else {
                $this->warn("    ⚠️  Monitoring data not integrated");
            }
            
            // Cleanup test data
            $iotData->delete();
            
        } catch (\Exception $e) {
            $this->error("    ❌ IoT Integration test failed: " . $e->getMessage());
        }
        
        $this->line('');
    }
    
    private function testMobileAppFeatures()
    {
        $this->info('📱 TESTING MOBILE APP FEATURES:');
        
        $pasien = Pasien::first();
        if (!$pasien) {
            $this->warn("  ⚠️  Tidak ada pasien untuk test mobile app");
            return;
        }
        
        $this->line("  📱 Testing dengan Pasien: {$pasien->user->nama_lengkap}");
        
        // Test personal recommendations
        try {
            $rekomendasi = RekomendasiHidrasi::hitungTargetDinamis($pasien->id);
            if ($rekomendasi) {
                $this->line("    ✅ Personal recommendations: {$rekomendasi->target_dinamis}ml");
                $this->line("      Alasan: {$rekomendasi->alasan_rekomendasi}");
            } else {
                $this->warn("    ⚠️  Personal recommendations failed");
            }
        } catch (\Exception $e) {
            $this->error("    ❌ Personal recommendations error: " . $e->getMessage());
        }
        
        // Test notification schedules
        try {
            $jadwal = JadwalNotifikasi::getJadwalAktif($pasien->id);
            $this->line("    ✅ Notification schedules: {$jadwal->count()} active schedules");
        } catch (\Exception $e) {
            $this->error("    ❌ Notification schedules error: " . $e->getMessage());
        }
        
        // Test monitoring data
        try {
            $monitoring = MonitoringDehidrasi::where('pasien_id', $pasien->id)
                ->whereDate('tanggal', today())
                ->sum('jumlah_minum_ml');
            $this->line("    ✅ Today's monitoring: {$monitoring}ml");
        } catch (\Exception $e) {
            $this->error("    ❌ Monitoring data error: " . $e->getMessage());
        }
        
        $this->line('');
    }
}
