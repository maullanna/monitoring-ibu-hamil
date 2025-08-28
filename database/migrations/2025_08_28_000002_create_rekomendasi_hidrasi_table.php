
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
        Schema::create('rekomendasi_hidrasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->date('tanggal')->comment('Tanggal rekomendasi');
            $table->integer('target_standar')->default(2000)->comment('Target standar dalam ml');
            $table->integer('target_dinamis')->comment('Target yang sudah dikalkulasi dalam ml');
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('Berat badan dalam kg');
            $table->decimal('suhu_cuaca', 4, 1)->nullable()->comment('Suhu cuaca dalam °C');
            $table->enum('aktivitas_fisik', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->tinyInteger('trimester_kehamilan')->nullable()->comment('Trimester kehamilan (1-3)');
            $table->integer('faktor_cuaca')->default(0)->comment('Tambahan target karena cuaca dalam ml');
            $table->integer('faktor_aktivitas')->default(0)->comment('Tambahan target karena aktivitas dalam ml');
            $table->integer('faktor_berat')->default(0)->comment('Tambahan target karena berat badan dalam ml');
            $table->integer('faktor_trimester')->default(0)->comment('Tambahan target karena trimester dalam ml');
            $table->text('alasan_rekomendasi')->comment('Penjelasan mengapa target berubah');
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['pasien_id', 'tanggal'], 'unique_pasien_tanggal');
            $table->index(['pasien_id', 'tanggal'], 'idx_pasien_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_hidrasi');
    }
};
