<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pasien;
use App\Models\MonitoringDehidrasi;
use App\Models\Notifikasi;
use App\Models\BackupLog;
use App\Models\PengaturanAplikasi;

class ShowDatabaseData extends Command
{
    protected $signature = 'db:show-data';
    protected $description = 'Show all data from all database tables';

    public function handle()
    {
        $this->info('=== DATABASE DATA OVERVIEW ===');
        
        // Users Table
        $this->info("\n📋 USERS TABLE:");
        $users = User::all();
        if ($users->count() > 0) {
            $this->table(
                ['ID', 'Nama Lengkap', 'Email', 'Role', 'Tanggal Dibuat'],
                $users->map(function($user) {
                    return [
                        $user->id,
                        $user->nama_lengkap,
                        $user->email,
                        $user->role,
                        $user->tanggal_dibuat
                    ];
                })
            );
        } else {
            $this->warn('Tidak ada data users');
        }

        // Pasien Table
        $this->info("\n🏥 PASIEN TABLE:");
        $pasien = Pasien::all();
        if ($pasien->count() > 0) {
            $this->table(
                ['ID', 'User ID', 'Tanggal Lahir', 'Alamat', 'Usia Kehamilan', 'Target Minum', 'Foto', 'Tanggal Dibuat'],
                $pasien->map(function($p) {
                    return [
                        $p->id,
                        $p->user_id,
                        $p->tanggal_lahir ? $p->tanggal_lahir->format('d/m/Y') : 'Belum diisi',
                        $p->alamat ?: 'Belum diisi',
                        $p->usia_kehamilan_minggu ? $p->usia_kehamilan_minggu . ' minggu' : 'Belum diisi',
                        $p->target_minum_ml . ' ml',
                        $p->foto ?: 'Tidak ada foto',
                        $p->tanggal_dibuat->format('d/m/Y')
                    ];
                })
            );
        } else {
            $this->warn('Tidak ada data pasien');
        }

        // Monitoring Dehidrasi Table
        $this->info("\n💧 MONITORING DEHIDRASI TABLE:");
        $monitoring = MonitoringDehidrasi::all();
        if ($monitoring->count() > 0) {
            $this->table(
                ['ID', 'Pasien ID', 'Tanggal', 'Jumlah Minum', 'Status', 'Tanggal Dibuat'],
                $monitoring->map(function($m) {
                    return [
                        $m->id,
                        $m->pasien_id,
                        $m->tanggal->format('d/m/Y'),
                        $m->jumlah_minum_ml . ' ml',
                        $m->status,
                        $m->created_at->format('d/m/Y H:i')
                    ];
                })
            );
        } else {
            $this->warn('Tidak ada data monitoring');
        }

        // Notifikasi Table
        $this->info("\n🔔 NOTIFIKASI TABLE:");
        $notifikasi = Notifikasi::all();
        if ($notifikasi->count() > 0) {
            $this->table(
                ['ID', 'Judul', 'Pesan', 'Status', 'Tanggal Dibuat'],
                $notifikasi->map(function($n) {
                    return [
                        $n->id,
                        $n->judul,
                        substr($n->pesan, 0, 50) . '...',
                        $n->status,
                        $n->created_at->format('d/m/Y H:i')
                    ];
                })
            );
        } else {
            $this->warn('Tidak ada data notifikasi');
        }

        // Backup Log Table
        $this->info("\n💾 BACKUP LOG TABLE:");
        $backupLog = BackupLog::all();
        if ($backupLog->count() > 0) {
            $this->table(
                ['ID', 'Nama File', 'Ukuran', 'Status', 'Tanggal Dibuat'],
                $backupLog->map(function($b) {
                    return [
                        $b->id,
                        $b->nama_file,
                        $b->ukuran_file,
                        $b->status,
                        $b->created_at->format('d/m/Y H:i')
                    ];
                })
            );
        } else {
            $this->warn('Tidak ada data backup log');
        }

        // Pengaturan Aplikasi Table
        $this->info("\n⚙️ PENGATURAN APLIKASI TABLE:");
        $pengaturan = PengaturanAplikasi::all();
        if ($pengaturan->count() > 0) {
            $this->table(
                ['ID', 'Nama Aplikasi', 'Versi', 'Deskripsi', 'Tanggal Dibuat'],
                $pengaturan->map(function($p) {
                    return [
                        $p->id,
                        $p->nama_aplikasi,
                        $p->versi,
                        substr($p->deskripsi, 0, 50) . '...',
                        $p->created_at->format('d/m/Y H:i')
                    ];
                })
            );
        } else {
            $this->warn('Tidak ada data pengaturan aplikasi');
        }

        // Database Statistics
        $this->info("\n📊 DATABASE STATISTICS:");
        $this->table(
            ['Tabel', 'Jumlah Record'],
            [
                ['Users', User::count()],
                ['Pasien', Pasien::count()],
                ['Monitoring Dehidrasi', MonitoringDehidrasi::count()],
                ['Notifikasi', Notifikasi::count()],
                ['Backup Log', BackupLog::count()],
                ['Pengaturan Aplikasi', PengaturanAplikasi::count()],
            ]
        );

        $this->info("\n✅ Data database berhasil ditampilkan!");
    }
}
