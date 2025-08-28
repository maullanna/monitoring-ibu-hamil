<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BackupLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string',
        ]);

        try {
            // Buat nama file backup
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            
            // Buat backup database
            $this->createDatabaseBackup($filename);
            
            // Simpan log backup
            BackupLog::create([
                'admin_id' => auth()->id(),
                'deskripsi' => $request->deskripsi,
                'status' => 'Sukses',
                'file_path' => $filename,
            ]);

            return redirect()->route('admin.backup')->with('success', 'Backup berhasil dibuat!');
            
        } catch (\Exception $e) {
            // Log error
            BackupLog::create([
                'admin_id' => auth()->id(),
                'deskripsi' => $request->deskripsi,
                'status' => 'Gagal',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->route('admin.backup')->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }
    
    public function download($id)
    {
        $backup = BackupLog::findOrFail($id);
        
        if ($backup->status !== 'Sukses' || !$backup->file_path) {
            return redirect()->route('admin.backup')->with('error', 'File backup tidak tersedia');
        }
        
        $filePath = storage_path("app/backups/{$backup->file_path}");
        
        if (!file_exists($filePath)) {
            return redirect()->route('admin.backup')->with('error', 'File backup tidak ditemukan');
        }
        
        return response()->download($filePath, $backup->file_path);
    }
    
    private function createDatabaseBackup($filename)
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        
        // Buat direktori backup jika belum ada
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $filePath = "{$backupDir}/{$filename}";
        
        // Command untuk mysqldump
        $command = "mysqldump --host={$host} --user={$username}";
        
        if ($password) {
            $command .= " --password={$password}";
        }
        
        $command .= " {$database} > {$filePath}";
        
        // Jalankan command
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Gagal membuat backup database');
        }
        
        return $filePath;
    }
}
