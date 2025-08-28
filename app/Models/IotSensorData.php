<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IotSensorData extends Model
{
    use HasFactory;

    protected $table = 'iot_sensor_data';

    protected $fillable = [
        'pasien_id',
        'device_id',
        'volume_ml',
        'sensor_type',
        'timestamp',
        'battery_level',
        'signal_strength',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'battery_level' => 'integer',
        'signal_strength' => 'integer',
    ];

    /**
     * Relasi ke model Pasien
     */
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    /**
     * Scope untuk data hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }

    /**
     * Scope untuk data minggu ini
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('timestamp', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope untuk data bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('timestamp', now()->month)
                    ->whereYear('timestamp', now()->year);
    }

    /**
     * Hitung total volume untuk periode tertentu
     */
    public static function getTotalVolume($pasienId, $startDate, $endDate)
    {
        return self::where('pasien_id', $pasienId)
                   ->whereBetween('timestamp', [$startDate, $endDate])
                   ->sum('volume_ml');
    }

    /**
     * Dapatkan data real-time untuk dashboard
     */
    public static function getRealTimeData($pasienId, $limit = 10)
    {
        return self::where('pasien_id', $pasienId)
                   ->orderBy('timestamp', 'desc')
                   ->limit($limit)
                   ->get();
    }
}
