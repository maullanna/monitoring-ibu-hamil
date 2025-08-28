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
        Schema::table('monitoring_dehidrasi', function (Blueprint $table) {
            $table->time('waktu_minum')->nullable()->after('jumlah_minum_ml')->comment('Waktu spesifik minum');
            $table->enum('sumber_data', ['manual', 'iot_sensor', 'api'])->default('manual')->after('waktu_minum');
            $table->string('device_id', 100)->nullable()->after('sumber_data')->comment('ID device IoT jika ada');
            $table->string('lokasi_minum', 100)->nullable()->after('device_id')->comment('Lokasi saat minum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_dehidrasi', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_minum',
                'sumber_data',
                'device_id',
                'lokasi_minum'
            ]);
        });
    }
};
