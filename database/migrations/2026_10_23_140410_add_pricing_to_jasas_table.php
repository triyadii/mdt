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
        Schema::table('jasas', function (Blueprint $table) {
            $table->string('harga_mulai')->nullable()->after('keteranganJasa');
            $table->string('estimasi_waktu')->nullable()->after('harga_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jasas', function (Blueprint $table) {
            $table->dropColumn(['harga_mulai', 'estimasi_waktu']);
        });
    }
};
