<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RekomendasiHidrasi;
use App\Models\Pasien;
use App\Models\JadwalNotifikasi;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Validator;

class RekomendasiController extends Controller
{
    /**
     * Tampilkan halaman rekomendasi personal
     */
    public function index()
    {
        $user = Auth::user();
        $pasien = $user->pasien;
        
        // Hitung rekomendasi hari ini
        $rekomendasiHariIni = RekomendasiHidrasi::hitungTargetDinamis($pasien->id);
        
        // Data rekomendasi minggu ini
        $rekomendasiMingguIni = RekomendasiHidrasi::where('pasien_id', $pasien->id)
            ->thisWeek()
            ->orderBy('tanggal')
            ->get();
        
        // Data rekomendasi bulan ini
        $rekomendasiBulanIni = RekomendasiHidrasi::where('pasien_id', $pasien->id)
            ->thisMonth()
            ->orderBy('tanggal')
            ->get();
        
        // Jadwal notifikasi aktif
        $jadwalNotifikasi = JadwalNotifikasi::getJadwalAktif($pasien->id);
        
        return view('user.rekomendasi', compact(
            'pasien',
            'rekomendasiHariIni',
            'rekomendasiMingguIni',
            'rekomendasiBulanIni',
            'jadwalNotifikasi'
        ));
    }

    /**
     * Update data personal untuk rekomendasi
     */
    public function updatePersonalData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'berat_badan' => 'nullable|numeric|min:30|max:200',
            'tinggi_badan' => 'nullable|numeric|min:100|max:250',
            'aktivitas_fisik' => 'required|in:rendah,sedang,tinggi',
            'lokasi_kota' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $pasien = $user->pasien;
            
            // Update data personal
            $pasien->update($validator->validated());
            
            // Hitung ulang rekomendasi
            $rekomendasi = RekomendasiHidrasi::hitungTargetDinamis($pasien->id);
            
            // Buat notifikasi perubahan target
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => '🎯 Target Minum Diperbarui!',
                'pesan' => "Target minum Anda telah diperbarui menjadi {$rekomendasi->target_dinamis}ml berdasarkan data personal terbaru.",
                'tipe' => 'info',
                'prioritas' => 'normal',
                'action_url' => '/user/rekomendasi',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data personal berhasil diperbarui',
                'data' => [
                    'target_baru' => $rekomendasi->target_dinamis,
                    'alasan' => $rekomendasi->alasan_rekomendasi,
                    'bmi' => $pasien->bmi,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update jadwal notifikasi
     */
    public function updateNotificationSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jadwal' => 'required|array',
            'jadwal.*.id' => 'nullable|exists:jadwal_notifikasi,id',
            'jadwal.*.waktu_notifikasi' => 'required|date_format:H:i',
            'jadwal.*.jenis_trigger' => 'required|in:waktu,volume,kombinasi',
            'jadwal.*.volume_threshold' => 'nullable|integer|min:100|max:5000',
            'jadwal.*.pesan_notifikasi' => 'required|string|max:500',
            'jadwal.*.is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $pasien = $user->pasien;
            
            // Hapus jadwal lama
            JadwalNotifikasi::where('pasien_id', $pasien->id)->delete();
            
            // Buat jadwal baru
            foreach ($request->jadwal as $jadwal) {
                JadwalNotifikasi::create([
                    'pasien_id' => $pasien->id,
                    'waktu_notifikasi' => $jadwal['waktu_notifikasi'],
                    'jenis_trigger' => $jadwal['jenis_trigger'],
                    'volume_threshold' => $jadwal['volume_threshold'],
                    'pesan_notifikasi' => $jadwal['pesan_notifikasi'],
                    'is_active' => $jadwal['is_active'],
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Jadwal notifikasi berhasil diperbarui',
                'data' => [
                    'total_jadwal' => count($request->jadwal),
                    'jadwal_aktif' => collect($request->jadwal)->where('is_active', true)->count(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan rekomendasi untuk periode tertentu
     */
    public function getRecommendations(Request $request)
    {
        $periode = $request->input('periode', 'today');
        $user = Auth::user();
        $pasien = $user->pasien;
        
        try {
            switch ($periode) {
                case 'today':
                    $data = RekomendasiHidrasi::getRekomendasiHariIni($pasien->id);
                    break;
                    
                case 'week':
                    $data = RekomendasiHidrasi::where('pasien_id', $pasien->id)
                        ->thisWeek()
                        ->orderBy('tanggal')
                        ->get();
                    break;
                    
                case 'month':
                    $data = RekomendasiHidrasi::where('pasien_id', $pasien->id)
                        ->thisMonth()
                        ->orderBy('tanggal')
                        ->get();
                    break;
                    
                default:
                    $data = RekomendasiHidrasi::getRekomendasiHariIni($pasien->id);
            }
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'periode' => $periode,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan statistik rekomendasi
     */
    public function getStatistics()
    {
        try {
            $user = Auth::user();
            $pasien = $user->pasien;
            
            // Statistik minggu ini
            $statistikMinggu = RekomendasiHidrasi::where('pasien_id', $pasien->id)
                ->thisWeek()
                ->selectRaw('
                    AVG(target_dinamis) as rata_rata_target,
                    AVG(faktor_cuaca) as rata_rata_faktor_cuaca,
                    AVG(faktor_aktivitas) as rata_rata_faktor_aktivitas,
                    AVG(faktor_berat) as rata_rata_faktor_berat,
                    AVG(faktor_trimester) as rata_rata_faktor_trimester
                ')
                ->first();
            
            // Statistik bulan ini
            $statistikBulan = RekomendasiHidrasi::where('pasien_id', $pasien->id)
                ->thisMonth()
                ->selectRaw('
                    AVG(target_dinamis) as rata_rata_target,
                    AVG(faktor_cuaca) as rata_rata_faktor_cuaca,
                    AVG(faktor_aktivitas) as rata_rata_faktor_aktivitas,
                    AVG(faktor_berat) as rata_rata_faktor_berat,
                    AVG(faktor_trimester) as rata_rata_faktor_trimester
                ')
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'minggu_ini' => $statistikMinggu,
                    'bulan_ini' => $statistikBulan,
                    'bmi_sekarang' => $pasien->bmi,
                    'status_hidrasi' => $pasien->status_hidrasi,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
