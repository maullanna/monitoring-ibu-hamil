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
        'nik',
        'tanggal_lahir',
        'alamat',
        'usia_kehamilan_minggu',
        'target_minum_ml',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_dibuat' => 'datetime',
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
}
