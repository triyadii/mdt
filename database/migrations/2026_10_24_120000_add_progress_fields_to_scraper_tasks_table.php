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
        Schema::table('scraper_tasks', function (Blueprint $table) {
            $table->integer('progress_percent')->default(0)->after('leads_count');
            $table->string('current_step')->nullable()->after('progress_percent');
            $table->integer('eta_seconds')->nullable()->after('current_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraper_tasks', function (Blueprint $table) {
            $table->dropColumn(['progress_percent', 'current_step', 'eta_seconds']);
        });
    }
};
