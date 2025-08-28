<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';

    protected $fillable = [
        'user_id',
        'tanggal_lahir',
        'alamat',
        'usia_kehamilan_minggu',
        'target_minum_ml',
        'foto',
        'berat_badan',
        'tinggi_badan',
        'aktivitas_fisik',
        'lokasi_kota',
        'target_minum_dinamis',
        'last_rekomendasi_update',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_dibuat' => 'datetime',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'last_rekomendasi_update' => 'datetime',
    ];

    /**
     * Get the user that owns the pasien.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the monitoring dehidrasi for the pasien.
     */
    public function monitoringDehidrasi()
    {
        return $this->hasMany(MonitoringDehidrasi::class);
    }

    /**
     * Get the IoT sensor data for the pasien.
     */
    public function iotSensorData()
    {
        return $this->hasMany(IotSensorData::class);
    }

    /**
     * Get the hydration recommendations for the pasien.
     */
    public function rekomendasiHidrasi()
    {
        return $this->hasMany(RekomendasiHidrasi::class);
    }

    /**
     * Get the notification schedules for the pasien.
     */
    public function jadwalNotifikasi()
    {
        return $this->hasMany(JadwalNotifikasi::class);
    }

    /**
     * Get the latest hydration recommendation.
     */
    public function rekomendasiHariIni()
    {
        return $this->hasOne(RekomendasiHidrasi::class)->whereDate('tanggal', today());
    }

    /**
     * Calculate BMI (Body Mass Index)
     */
    public function getBmiAttribute()
    {
        if ($this->tinggi_badan && $this->berat_badan) {
            $tinggiMeter = $this->tinggi_badan / 100;
            return round($this->berat_badan / ($tinggiMeter * $tinggiMeter), 2);
        }
        return null;
    }

    /**
     * Get hydration status based on today's intake
     */
    public function getStatusHidrasiAttribute()
    {
        $targetHariIni = $this->target_minum_dinamis ?? $this->target_minum_ml;
        $totalHariIni = $this->monitoringDehidrasi()
            ->whereDate('tanggal', today())
            ->sum('jumlah_minum_ml');

        if ($totalHariIni >= $targetHariIni) {
            return 'terpenuhi';
        } elseif ($totalHariIni >= ($targetHariIni * 0.8)) {
            return 'hampir_terpenuhi';
        } elseif ($totalHariIni >= ($targetHariIni * 0.5)) {
            return 'kurang';
        } else {
            return 'sangat_kurang';
        }
    }
}
