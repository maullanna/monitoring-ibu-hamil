<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RekomendasiHidrasi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_hidrasi';

    protected $fillable = [
        'pasien_id',
        'tanggal',
        'target_standar',
        'target_dinamis',
        'berat_badan',
        'suhu_cuaca',
        'aktivitas_fisik',
        'trimester_kehamilan',
        'faktor_cuaca',
        'faktor_aktivitas',
        'faktor_berat',
        'faktor_trimester',
        'alasan_rekomendasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'target_standar' => 'integer',
        'target_dinamis' => 'integer',
        'berat_badan' => 'decimal:2',
        'suhu_cuaca' => 'decimal:1',
        'faktor_cuaca' => 'integer',
        'faktor_aktivitas' => 'integer',
        'faktor_berat' => 'integer',
        'faktor_trimester' => 'integer',
    ];

    /**
     * Relasi ke model Pasien
     */
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    /**
     * Scope untuk rekomendasi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    /**
     * Scope untuk rekomendasi minggu ini
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('tanggal', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope untuk rekomendasi bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year);
    }

    /**
     * Hitung target dinamis berdasarkan faktor
     */
    public static function hitungTargetDinamis($pasienId, $tanggal = null)
    {
        $tanggal = $tanggal ?? today();
        $pasien = Pasien::find($pasienId);
        
        if (!$pasien) {
            return null;
        }

        $targetStandar = 2000; // Target standar 2L
        $faktorCuaca = 0;
        $faktorAktivitas = 0;
        $faktorBerat = 0;
        $faktorTrimester = 0;
        $alasan = [];

        // Faktor cuaca (jika ada data cuaca)
        if ($pasien->lokasi_kota) {
            // Simulasi data cuaca (nanti bisa integrasi API cuaca)
            $suhuCuaca = rand(25, 35); // Simulasi suhu 25-35°C
            
            if ($suhuCuaca > 30) {
                $faktorCuaca = 300; // Tambah 300ml jika cuaca panas
                $alasan[] = "Cuaca panas ({$suhuCuaca}°C) - tambah 300ml";
            }
        }

        // Faktor aktivitas fisik
        switch ($pasien->aktivitas_fisik) {
            case 'tinggi':
                $faktorAktivitas = 200;
                $alasan[] = "Aktivitas tinggi - tambah 200ml";
                break;
            case 'sedang':
                $faktorAktivitas = 100;
                $alasan[] = "Aktivitas sedang - tambah 100ml";
                break;
            case 'rendah':
                $faktorAktivitas = 0;
                $alasan[] = "Aktivitas rendah - target standar";
                break;
        }

        // Faktor berat badan
        if ($pasien->berat_badan) {
            if ($pasien->berat_badan > 80) {
                $faktorBerat = 200;
                $alasan[] = "Berat badan > 80kg - tambah 200ml";
            } elseif ($pasien->berat_badan < 50) {
                $faktorBerat = -100;
                $alasan[] = "Berat badan < 50kg - kurangi 100ml";
            }
        }

        // Faktor trimester kehamilan
        if ($pasien->usia_kehamilan_minggu) {
            if ($pasien->usia_kehamilan_minggu <= 12) {
                $faktorTrimester = 100; // Trimester 1
                $alasan[] = "Trimester 1 - tambah 100ml";
            } elseif ($pasien->usia_kehamilan_minggu <= 28) {
                $faktorTrimester = 200; // Trimester 2
                $alasan[] = "Trimester 2 - tambah 200ml";
            } else {
                $faktorTrimester = 300; // Trimester 3
                $alasan[] = "Trimester 3 - tambah 300ml";
            }
        }

        $targetDinamis = $targetStandar + $faktorCuaca + $faktorAktivitas + $faktorBerat + $faktorTrimester;
        
        // Pastikan target minimal 1500ml
        $targetDinamis = max(1500, $targetDinamis);

        // Buat atau update rekomendasi
        $rekomendasi = self::updateOrCreate(
            [
                'pasien_id' => $pasienId,
                'tanggal' => $tanggal
            ],
            [
                'target_standar' => $targetStandar,
                'target_dinamis' => $targetDinamis,
                'berat_badan' => $pasien->berat_badan,
                'suhu_cuaca' => $suhuCuaca ?? null,
                'aktivitas_fisik' => $pasien->aktivitas_fisik,
                'trimester_kehamilan' => $pasien->usia_kehamilan_minggu ? 
                    ($pasien->usia_kehamilan_minggu <= 12 ? 1 : ($pasien->usia_kehamilan_minggu <= 28 ? 2 : 3)) : null,
                'faktor_cuaca' => $faktorCuaca,
                'faktor_aktivitas' => $faktorAktivitas,
                'faktor_berat' => $faktorBerat,
                'faktor_trimester' => $faktorTrimester,
                'alasan_rekomendasi' => implode(', ', $alasan),
            ]
        );

        // Update target dinamis di tabel pasien
        $pasien->update([
            'target_minum_dinamis' => $targetDinamis,
            'last_rekomendasi_update' => now()
        ]);

        return $rekomendasi;
    }

    /**
     * Dapatkan rekomendasi hari ini
     */
    public static function getRekomendasiHariIni($pasienId)
    {
        return self::where('pasien_id', $pasienId)
                   ->whereDate('tanggal', today())
                   ->first();
    }
}
