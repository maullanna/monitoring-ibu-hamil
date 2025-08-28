<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonitoringDehidrasi;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = MonitoringDehidrasi::with(['pasien.user']);
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $monitoringData = $query->latest()->get();
        
        return view('admin.monitoring', compact('monitoringData'));
    }
    
    public function export(Request $request, $format = 'excel')
    {
        $query = MonitoringDehidrasi::with(['pasien.user']);
        
        // Apply same filters as index
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $data = $query->latest()->get();
        
        switch ($format) {
            case 'csv':
                return $this->exportCsv($data);
            case 'pdf':
                return $this->exportPdf($data);
            default:
                return $this->exportExcel($data);
        }
    }
    
    private function exportCsv($data)
    {
        $filename = 'monitoring_data_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Nama Pasien', 'Email', 'Tanggal', 'Jumlah Minum (ml)', 
                'Target (ml)', 'Pencapaian (%)', 'Status', 'Waktu Input'
            ]);
            
            // Data
            foreach ($data as $row) {
                $target = $row->pasien->target_minum_ml ?? 2000;
                $percentage = min(100, ($row->jumlah_minum_ml / $target) * 100);
                
                fputcsv($file, [
                    $row->id,
                    $row->pasien->user->nama_lengkap,
                    $row->pasien->user->email,
                    $row->tanggal->format('d/m/Y'),
                    $row->jumlah_minum_ml,
                    $target,
                    number_format($percentage, 1),
                    $row->status ?? 'Belum dinilai',
                    $row->created_at->format('d/m/Y H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportExcel($data)
    {
        // For now, redirect to CSV (Excel requires additional packages)
        return $this->exportCsv($data);
    }
    
    private function exportPdf($data)
    {
        // For now, redirect to CSV (PDF requires additional packages)
        return $this->exportCsv($data);
    }
}
