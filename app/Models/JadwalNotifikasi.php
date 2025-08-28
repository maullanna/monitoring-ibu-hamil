<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalNotifikasi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_notifikasi';

    protected $fillable = [
        'pasien_id',
        'waktu_notifikasi',
        'jenis_trigger',
        'volume_threshold',
        'pesan_notifikasi',
        'is_active',
    ];

    protected $casts = [
        'waktu_notifikasi' => 'datetime',
        'volume_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke model Pasien
     */
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    /**
     * Scope untuk jadwal aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk jadwal berdasarkan waktu
     */
    public function scopeByTime($query, $time)
    {
        return $query->whereTime('waktu_notifikasi', $time);
    }

    /**
     * Buat jadwal notifikasi default untuk pasien baru
     */
    public static function buatJadwalDefault($pasienId)
    {
        $jadwalDefault = [
            [
                'waktu_notifikasi' => '08:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Selamat pagi! Jangan lupa minum air untuk memulai hari yang sehat.',
            ],
            [
                'waktu_notifikasi' => '10:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Waktunya minum air! Jaga kesehatan Anda dan bayi.',
            ],
            [
                'waktu_notifikasi' => '12:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Siang hari, jangan lupa minum air yang cukup!',
            ],
            [
                'waktu_notifikasi' => '15:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Sore hari, tetap jaga hidrasi tubuh Anda.',
            ],
            [
                'waktu_notifikasi' => '18:00:00',
                'jenis_trigger' => 'waktu',
                'pesan_notifikasi' => 'Malam hari, pastikan target minum hari ini tercapai.',
            ],
            [
                'waktu_notifikasi' => '20:00:00',
                'jenis_trigger' => 'volume',
                'volume_threshold' => 1500,
                'pesan_notifikasi' => 'Target minum Anda belum tercapai. Segera minum air!',
            ],
        ];

        foreach ($jadwalDefault as $jadwal) {
            self::create([
                'pasien_id' => $pasienId,
                'waktu_notifikasi' => $jadwal['waktu_notifikasi'],
                'jenis_trigger' => $jadwal['jenis_trigger'],
                'volume_threshold' => $jadwal['volume_threshold'] ?? null,
                'pesan_notifikasi' => $jadwal['pesan_notifikasi'],
                'is_active' => true,
            ]);
        }
    }

    /**
     * Dapatkan jadwal aktif untuk pasien
     */
    public static function getJadwalAktif($pasienId)
    {
        return self::where('pasien_id', $pasienId)
                   ->where('is_active', true)
                   ->orderBy('waktu_notifikasi')
                   ->get();
    }

    /**
     * Check apakah sudah waktunya notifikasi
     */
    public function isWaktuNotifikasi()
    {
        $now = now();
        $waktuNotifikasi = $this->waktu_notifikasi;
        
        // Check apakah sudah waktunya (dalam rentang 5 menit)
        return $now->diffInMinutes($waktuNotifikasi, false) <= 5 && 
               $now->diffInMinutes($waktuNotifikasi, false) >= 0;
    }
}
