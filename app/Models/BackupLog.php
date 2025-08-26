<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    use HasFactory;

    protected $table = 'backup_log';

    protected $fillable = [
        'admin_id',
        'deskripsi',
        'status',
    ];

    /**
     * Get the admin that created the backup.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
