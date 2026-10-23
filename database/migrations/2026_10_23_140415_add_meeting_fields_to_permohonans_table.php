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
        Schema::table('permohonans', function (Blueprint $table) {
            $table->date('tanggal_konsultasi')->nullable()->after('keteranganPermohonan');
            $table->string('jam_konsultasi', 20)->nullable()->after('tanggal_konsultasi');
            $table->string('status', 30)->default('pending')->after('jam_konsultasi');
            $table->string('telegram_message_id', 100)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_konsultasi', 'jam_konsultasi', 'status', 'telegram_message_id']);
        });
    }
};
