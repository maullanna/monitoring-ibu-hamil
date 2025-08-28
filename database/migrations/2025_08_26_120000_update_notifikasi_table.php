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
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->string('tipe')->default('info')->after('pesan'); // info, warning, success, danger
            $table->string('prioritas')->default('normal')->after('tipe'); // low, normal, high, urgent
            $table->string('action_url')->nullable()->after('prioritas'); // URL untuk action button
            $table->timestamp('expires_at')->nullable()->after('action_url'); // Kapan notifikasi expired
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'prioritas', 'action_url', 'expires_at']);
        });
    }
};
