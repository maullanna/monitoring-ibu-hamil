<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengaturanAplikasi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PengaturanController extends Controller
{
    public function index()
    {
        // Debug info
        $pengaturan = PengaturanAplikasi::first();
        Log::info('Pengaturan index called', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'unknown',
            'pengaturan_exists' => $pengaturan ? true : false,
            'pengaturan_id' => $pengaturan ? $pengaturan->id : null
        ]);
        
        return view('admin.pengaturan', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        // Debug logging
        Log::info('Pengaturan update called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $request->validate([
            'nama_aplikasi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Start transaction
            DB::beginTransaction();
            
            $pengaturan = PengaturanAplikasi::first();
            
            if (!$pengaturan) {
                $pengaturan = new PengaturanAplikasi();
                Log::info('Creating new pengaturan record');
            } else {
                Log::info('Updating existing pengaturan', ['id' => $pengaturan->id]);
            }

            $pengaturan->nama_aplikasi = $request->nama_aplikasi;
            $pengaturan->deskripsi = $request->deskripsi;

            // Handle logo upload
            if ($request->hasFile('logo')) {
                Log::info('Logo file detected', ['filename' => $request->file('logo')->getClientOriginalName()]);
                
                // Delete old logo if exists
                if ($pengaturan->logo && Storage::disk('public')->exists($pengaturan->logo)) {
                    Storage::disk('public')->delete($pengaturan->logo);
                    Log::info('Old logo deleted', ['path' => $pengaturan->logo]);
                }

                $logoPath = $request->file('logo')->store('logos', 'public');
                $pengaturan->logo = $logoPath;
                Log::info('New logo stored', ['path' => $logoPath]);
            }

            $pengaturan->save();
            
            // Commit transaction
            DB::commit();
            
            Log::info('Pengaturan saved successfully', ['id' => $pengaturan->id]);

            return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan berhasil diupdate!');
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollback();
            
            Log::error('Error updating pengaturan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.pengaturan')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
