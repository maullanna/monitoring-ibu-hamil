<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->time('waktu_notifikasi')->comment('Waktu notifikasi dalam format HH:MM');
            $table->enum('jenis_trigger', ['waktu', 'volume', 'kombinasi'])->default('waktu');
            $table->integer('volume_threshold')->nullable()->comment('Threshold volume dalam ml untuk trigger');
            $table->text('pesan_notifikasi')->comment('Pesan notifikasi yang akan ditampilkan');
            $table->boolean('is_active')->default(true)->comment('Status aktif tidaknya jadwal');
            $table->timestamps();
            
            // Indexes untuk performa query
            $table->index(['pasien_id', 'waktu_notifikasi'], 'idx_pasien_waktu');
            $table->index('is_active', 'idx_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_notifikasi');
    }
};
