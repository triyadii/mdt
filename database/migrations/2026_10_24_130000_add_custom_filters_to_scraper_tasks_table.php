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
            $table->boolean('filter_no_website')->default(true)->after('location');
            $table->boolean('filter_has_phone')->default(true)->after('filter_no_website');
            $table->integer('min_reviews')->default(5)->after('filter_has_phone');
            $table->decimal('min_rating', 3, 2)->default(0)->after('min_reviews');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraper_tasks', function (Blueprint $table) {
            $table->dropColumn(['filter_no_website', 'filter_has_phone', 'min_reviews', 'min_rating']);
        });
    }
};
