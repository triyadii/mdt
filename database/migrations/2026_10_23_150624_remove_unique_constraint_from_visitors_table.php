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
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropUnique('visitors_ip_address_visited_date_unique');
            $table->index(['ip_address', 'visited_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['ip_address', 'visited_date']);
            $table->unique(['ip_address', 'visited_date']);
        });
    }
};
