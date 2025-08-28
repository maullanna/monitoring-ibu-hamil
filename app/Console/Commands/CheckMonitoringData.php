<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonitoringDehidrasi;
use App\Models\Pasien;
use App\Models\User;

class CheckMonitoringData extends Command
{
    protected $signature = 'monitoring:check';
    protected $description = 'Check monitoring data for debugging';

    public function handle()
    {
        $this->info('=== CHECKING MONITORING DATA ===');
        
        // Check MonitoringDehidrasi data
        $this->info("\n📊 MONITORING DEHIDRASI DATA:");
        $monitoringData = MonitoringDehidrasi::with(['pasien.user'])->get();
        
        if ($monitoringData->count() > 0) {
            $this->table(
                ['ID', 'Pasien', 'Tanggal', 'Jumlah (ml)', 'Status', 'Target'],
                $monitoringData->map(function($item) {
                    return [
                        $item->id,
                        $item->pasien->user->nama_lengkap,
                        $item->tanggal->format('d/m/Y'),
                        $item->jumlah_minum_ml,
                        $item->status ?? 'NULL',
                        ($item->pasien->target_minum_ml ?? 2000) . ' ml'
                    ];
                })
            );
        } else {
            $this->warn('Tidak ada data monitoring');
        }
        
        // Check status distribution
        $this->info("\n📈 STATUS DISTRIBUTION:");
        $statusCounts = MonitoringDehidrasi::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
            
        foreach ($statusCounts as $status => $count) {
            $this->line("Status '{$status}': {$count} records");
        }
        
        // Check all records individually
        $this->info("\n🔍 ALL RECORDS DETAIL:");
        $allRecords = MonitoringDehidrasi::orderBy('id')->get();
        foreach ($allRecords as $record) {
            $this->line("ID {$record->id}: Tanggal {$record->tanggal->format('d/m/Y')}, Jumlah {$record->jumlah_minum_ml}ml, Status '{$record->status}'");
        }
        
        // Check pasien data
        $this->info("\n🏥 PASIEN DATA:");
        $pasien = Pasien::with('user')->first();
        if ($pasien) {
            $this->line("ID: {$pasien->id}");
            $this->line("User: {$pasien->user->nama_lengkap}");
            $this->line("Target Minum: {$pasien->target_minum_ml} ml");
            $this->line("Usia Kehamilan: {$pasien->usia_kehamilan_minggu} minggu");
        }
        
        $this->info("\n✅ Monitoring data check completed!");
    }
}
