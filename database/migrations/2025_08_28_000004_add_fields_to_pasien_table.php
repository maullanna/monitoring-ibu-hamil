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
        Schema::table('pasien', function (Blueprint $table) {
            $table->decimal('berat_badan', 5, 2)->nullable()->after('target_minum_ml')->comment('Berat badan dalam kg');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->after('berat_badan')->comment('Tinggi badan dalam cm');
            $table->enum('aktivitas_fisik', ['rendah', 'sedang', 'tinggi'])->default('sedang')->after('tinggi_badan');
            $table->string('lokasi_kota', 100)->nullable()->after('aktivitas_fisik')->comment('Kota untuk data cuaca');
            $table->integer('target_minum_dinamis')->nullable()->after('lokasi_kota')->comment('Target minum yang sudah dikalkulasi');
            $table->timestamp('last_rekomendasi_update')->nullable()->after('target_minum_dinamis')->comment('Terakhir update rekomendasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropColumn([
                'berat_badan',
                'tinggi_badan', 
                'aktivitas_fisik',
                'lokasi_kota',
                'target_minum_dinamis',
                'last_rekomendasi_update'
            ]);
        });
    }
};
