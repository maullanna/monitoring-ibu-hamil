<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonitoringDehidrasi;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\BackupLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Data untuk chart asupan air minum (7 hari terakhir)
        $chartData = $this->getWaterIntakeChartData();
        
        return view('admin.dashboard', compact('chartData'));
    }

    private function getWaterIntakeChartData()
    {
        $labels = [];
        $data = [];
        
        // Generate 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            // Hitung rata-rata asupan air untuk tanggal tersebut
            $avgIntake = MonitoringDehidrasi::whereDate('tanggal', $date->format('Y-m-d'))
                ->avg('jumlah_minum_ml');
            
            $data[] = round($avgIntake ?: 0);
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
