<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IotSensorData;
use App\Models\MonitoringDehidrasi;
use App\Models\Pasien;
use App\Models\RekomendasiHidrasi;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class IotController extends Controller
{
    /**
     * Terima data dari IoT sensor botol
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'pasien_id' => 'required|exists:pasien,id',
                'device_id' => 'required|string|max:100',
                'volume_ml' => 'required|integer|min:1|max:1000',
                'sensor_type' => 'required|in:flow_meter,weight_sensor,manual',
                'timestamp' => 'nullable|date',
                'battery_level' => 'nullable|integer|min:0|max:100',
                'signal_strength' => 'nullable|integer|min:0|max:100',
                'lokasi_minum' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['timestamp'] = $data['timestamp'] ?? now();

            // Simpan data IoT sensor
            $iotData = IotSensorData::create($data);

            // Simpan ke monitoring dehidrasi
            $monitoringData = MonitoringDehidrasi::create([
                'pasien_id' => $data['pasien_id'],
                'tanggal' => $data['timestamp']->format('Y-m-d'),
                'jumlah_minum_ml' => $data['volume_ml'],
                'waktu_minum' => $data['timestamp'],
                'sumber_data' => 'iot_sensor',
                'device_id' => $data['device_id'],
                'lokasi_minum' => $data['lokasi_minum'] ?? 'IoT Botol',
                'status' => 'terpenuhi',
            ]);

            // Update rekomendasi target dinamis
            $rekomendasi = RekomendasiHidrasi::hitungTargetDinamis($data['pasien_id']);

            // Buat notifikasi jika volume melebihi target
            $pasien = Pasien::find($data['pasien_id']);
            $totalHariIni = $pasien->monitoringDehidrasi()
                ->whereDate('tanggal', today())
                ->sum('jumlah_minum_ml');

            if ($totalHariIni >= $rekomendasi->target_dinamis) {
                Notifikasi::create([
                    'user_id' => $pasien->user_id,
                    'judul' => '🎉 Target Minum Hari Ini Tercapai!',
                    'pesan' => "Selamat! Anda telah mencapai target minum hari ini ({$rekomendasi->target_dinamis}ml). Terus jaga kesehatan!",
                    'tipe' => 'success',
                    'prioritas' => 'normal',
                    'action_url' => '/user/monitoring',
                ]);
            }

            // Log aktivitas
            Log::info('IoT Data received', [
                'pasien_id' => $data['pasien_id'],
                'device_id' => $data['device_id'],
                'volume_ml' => $data['volume_ml'],
                'timestamp' => $data['timestamp']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data IoT berhasil disimpan',
                'data' => [
                    'iot_id' => $iotData->id,
                    'monitoring_id' => $monitoringData->id,
                    'total_hari_ini' => $totalHariIni,
                    'target_hari_ini' => $rekomendasi->target_dinamis,
                    'status_hidrasi' => $pasien->status_hidrasi,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error saving IoT data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan data real-time untuk dashboard
     */
    public function getRealTimeData(Request $request)
    {
        try {
            $pasienId = $request->input('pasien_id');
            
            if (!$pasienId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasien ID diperlukan'
                ], 400);
            }

            // Data IoT hari ini
            $iotDataHariIni = IotSensorData::where('pasien_id', $pasienId)
                ->today()
                ->orderBy('timestamp', 'desc')
                ->get();

            // Total volume hari ini
            $totalVolumeHariIni = $iotDataHariIni->sum('volume_ml');

            // Data 7 hari terakhir
            $data7Hari = IotSensorData::where('pasien_id', $pasienId)
                ->whereBetween('timestamp', [
                    now()->subDays(6)->startOfDay(),
                    now()->endOfDay()
                ])
                ->selectRaw('DATE(timestamp) as tanggal, SUM(volume_ml) as total_volume')
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get();

            // Status device
            $latestDeviceData = IotSensorData::where('pasien_id', $pasienId)
                ->whereNotNull('battery_level')
                ->orderBy('timestamp', 'desc')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'real_time' => [
                        'total_hari_ini' => $totalVolumeHariIni,
                        'jumlah_sensor_aktif' => $iotDataHariIni->count(),
                        'last_update' => $iotDataHariIni->first()?->timestamp,
                    ],
                    'chart_7_hari' => $data7Hari,
                    'device_status' => $latestDeviceData ? [
                        'battery_level' => $latestDeviceData->battery_level,
                        'signal_strength' => $latestDeviceData->signal_strength,
                        'last_seen' => $latestDeviceData->timestamp,
                    ] : null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting IoT real-time data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan statistik IoT untuk periode tertentu
     */
    public function getStatistics(Request $request)
    {
        try {
            $pasienId = $request->input('pasien_id');
            $periode = $request->input('periode', 'week'); // week, month, year
            
            if (!$pasienId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasien ID diperlukan'
                ], 400);
            }

            $startDate = null;
            $endDate = now();

            switch ($periode) {
                case 'week':
                    $startDate = now()->subWeek()->startOfDay();
                    break;
                case 'month':
                    $startDate = now()->subMonth()->startOfDay();
                    break;
                case 'year':
                    $startDate = now()->subYear()->startOfDay();
                    break;
                default:
                    $startDate = now()->subWeek()->startOfDay();
            }

            $statistics = IotSensorData::where('pasien_id', $pasienId)
                ->whereBetween('timestamp', [$startDate, $endDate])
                ->selectRaw('
                    COUNT(*) as total_records,
                    SUM(volume_ml) as total_volume,
                    AVG(volume_ml) as rata_rata_volume,
                    MIN(volume_ml) as min_volume,
                    MAX(volume_ml) as max_volume,
                    COUNT(DISTINCT DATE(timestamp)) as hari_aktif
                ')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'periode' => $periode,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'statistics' => $statistics,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting IoT statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
