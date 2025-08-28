<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'is_read',
        'tipe',
        'prioritas',
        'action_url',
        'expires_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notifikasi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
