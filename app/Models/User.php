<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'role',
        'qr_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tanggal_dibuat' => 'datetime',
    ];

    /**
     * Get the pasien associated with the user.
     */
    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }

    /**
     * Get the notifikasi for the user.
     */
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * Get the backup logs created by the admin.
     */
    public function backupLogs()
    {
        return $this->hasMany(BackupLog::class, 'admin_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
