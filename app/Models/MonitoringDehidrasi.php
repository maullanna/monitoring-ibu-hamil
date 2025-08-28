<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringDehidrasi extends Model
{
    use HasFactory;

    protected $table = 'monitoring_dehidrasi';

    protected $fillable = [
        'pasien_id',
        'tanggal',
        'jumlah_minum_ml',
        'status',
        'waktu_minum',
        'sumber_data',
        'device_id',
        'lokasi_minum',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_minum' => 'datetime',
    ];

    /**
     * Get the pasien that owns the monitoring.
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    /**
     * Get the user through pasien.
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Pasien::class, 'id', 'id', 'pasien_id', 'user_id');
    }
}
