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
        Schema::create('iot_sensor_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->string('device_id', 100)->comment('ID unik device IoT botol');
            $table->integer('volume_ml')->comment('Volume air yang diminum dalam ml');
            $table->enum('sensor_type', ['flow_meter', 'weight_sensor', 'manual'])->default('flow_meter');
            $table->timestamp('timestamp')->useCurrent()->comment('Waktu sensor membaca data');
            $table->integer('battery_level')->nullable()->comment('Level baterai device dalam %');
            $table->integer('signal_strength')->nullable()->comment('Kekuatan sinyal dalam %');
            $table->timestamps();
            
            // Indexes untuk performa query
            $table->index(['pasien_id', 'timestamp'], 'idx_pasien_timestamp');
            $table->index(['device_id', 'timestamp'], 'idx_device_timestamp');
            $table->index('timestamp', 'idx_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iot_sensor_data');
    }
};
