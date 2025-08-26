<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MonitoringDehidrasi;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        $pasien = Auth::user()->pasien;
        
        if (!$pasien) {
            return view('user.monitoring')->with('error', 'Data pasien tidak ditemukan');
        }

        // Ambil data 30 hari terakhir untuk grafik
        $chartData = $this->getChartData($pasien->id);
        
        return view('user.monitoring', compact('chartData'));
    }

    /**
     * Ambil data untuk grafik 1 bulan ke depan (dari hari ini sampai September)
     */
    private function getChartData($pasienId)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->endOfMonth();
        
        // Jika sudah September, gunakan 30 hari ke depan
        if ($startDate->month >= 9) {
            $endDate = $startDate->copy()->addDays(30);
        }
        
        // Buat array 1 bulan dengan data kosong
        $chartData = [];
        $labels = [];
        $currentDate = clone $startDate;
        
        while ($currentDate->lte($endDate)) {
            $labels[] = $currentDate->format('d/m');
            
            // Cari data dari database untuk tanggal ini
            $monitoring = MonitoringDehidrasi::where('pasien_id', $pasienId)
                ->where('tanggal', $currentDate->format('Y-m-d'))
                ->first();
            
            // Jika ada data, gunakan jumlah minum, jika tidak gunakan 0
            $chartData[] = $monitoring ? $monitoring->jumlah_minum_ml : 0;
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => $labels,
            'data' => $chartData
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah_minum_ml' => 'required|integer|min:0|max:5000',
        ]);

        // Check if already exists for today
        $existing = MonitoringDehidrasi::where('pasien_id', Auth::user()->pasien->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return redirect()->route('user.monitoring')->with('error', 'Data untuk tanggal ini sudah ada!');
        }

        // Determine status based on target
        $target = Auth::user()->pasien->target_minum_ml ?? 2000;
        $status = null;
        
        if ($request->jumlah_minum_ml < ($target * 0.8)) {
            $status = 'Kurang';
        } elseif ($request->jumlah_minum_ml >= $target) {
            $status = 'Cukup';
        } else {
            $status = 'Berlebihan';
        }

        $monitoring = MonitoringDehidrasi::create([
            'pasien_id' => Auth::user()->pasien->id,
            'tanggal' => $request->tanggal,
            'jumlah_minum_ml' => $request->jumlah_minum_ml,
            'status' => $status,
        ]);

        // Debug log
        \Log::info('Monitoring data created', [
            'id' => $monitoring->id,
            'pasien_id' => $monitoring->pasien_id,
            'tanggal' => $monitoring->tanggal,
            'jumlah_minum_ml' => $monitoring->jumlah_minum_ml,
            'status' => $monitoring->status
        ]);

        return redirect()->route('user.monitoring')->with('success', 'Data monitoring berhasil disimpan!');
    }

    /**
     * API endpoint untuk mendapatkan data grafik (untuk IoT)
     */
    public function getChartDataApi()
    {
        $pasien = Auth::user()->pasien;
        
        if (!$pasien) {
            return response()->json(['error' => 'Data pasien tidak ditemukan'], 404);
        }

        $chartData = $this->getChartData($pasien->id);
        
        return response()->json($chartData);
    }
}
