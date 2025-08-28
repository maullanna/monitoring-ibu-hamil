<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MonitoringController;
use App\Http\Controllers\Api\IotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// IoT API Routes untuk monitoring air minum
Route::prefix('iot')->group(function () {
    // Endpoint untuk IoT device mengirim data sensor
    Route::post('/sensor-data', [IotController::class, 'store'])->name('iot.sensor-data.store');
    
    // Endpoint untuk mendapatkan data real-time
    Route::get('/real-time-data', [IotController::class, 'getRealTimeData'])->name('iot.real-time-data');
    
    // Endpoint untuk mendapatkan statistik IoT
    Route::get('/statistics', [IotController::class, 'getStatistics'])->name('iot.statistics');
    
    // Legacy endpoint untuk backward compatibility
    Route::post('/monitoring', function (Request $request) {
        $request->validate([
            'device_id' => 'required|string',
            'pasien_id' => 'required|integer|exists:pasien,id',
            'jumlah_minum_ml' => 'required|integer|min:0|max:5000',
            'timestamp' => 'required|date',
        ]);

        $monitoring = \App\Models\MonitoringDehidrasi::updateOrCreate(
            [
                'pasien_id' => $request->pasien_id,
                'tanggal' => $request->timestamp,
            ],
            [
                'jumlah_minum_ml' => $request->jumlah_minum_ml,
                'status' => null,
            ]
        );

        $pasien = \App\Models\Pasien::find($request->pasien_id);
        $target = $pasien->target_minum_ml ?? 2000;
        
        if ($request->jumlah_minum_ml < ($target * 0.8)) {
            $status = 'Kurang';
        } elseif ($request->jumlah_minum_ml >= $target) {
            $status = 'Cukup';
        } else {
            $status = 'Berlebihan';
        }

        $monitoring->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'Data monitoring berhasil disimpan',
            'data' => [
                'id' => $monitoring->id,
                'jumlah_minum_ml' => $request->jumlah_minum_ml,
                'status' => $status,
                'target' => $target,
                'pencapaian' => min(100, ($request->jumlah_minum_ml / $target) * 100)
            ]
        ]);
    });

    // Endpoint untuk IoT device mengambil data grafik 30 hari
    Route::get('/monitoring/{pasien_id}/chart', function ($pasienId) {
        $endDate = \Carbon\Carbon::now();
        $startDate = \Carbon\Carbon::now()->subDays(29);
        
        $chartData = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $monitoring = \App\Models\MonitoringDehidrasi::where('pasien_id', $pasienId)
                ->where('tanggal', $date->format('Y-m-d'))
                ->first();
            
            $chartData[] = $monitoring ? $monitoring->jumlah_minum_ml : 0;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Data grafik berhasil diambil',
            'data' => [
                'labels' => $labels,
                'data' => $chartData
            ]
        ]);
    });
});
