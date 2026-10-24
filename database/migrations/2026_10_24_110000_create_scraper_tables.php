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
        if (! Schema::hasTable('scraper_tasks')) {
            Schema::create('scraper_tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('keyword'); // misal: "cafe", "kuliner", "coffee shop"
                $table->string('location'); // misal: "Medan Selayang", "Medan Baru"
                $table->enum('frequency', ['once', 'daily', 'weekly'])->default('once');
                $table->string('status')->default('pending'); // pending, running, completed, failed
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('last_run_at')->nullable();
                $table->integer('leads_count')->default(0);
                $table->text('log_message')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('umkm_leads')) {
            Schema::create('umkm_leads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->nullable()->constrained('scraper_tasks')->nullOnDelete();
                $table->string('place_id')->nullable()->index();
                $table->string('name');
                $table->string('category')->nullable();
                $table->string('phone_raw')->nullable();
                $table->string('phone_wa')->nullable()->index();
                $table->text('address')->nullable();
                $table->integer('review_count')->default(0);
                $table->decimal('rating', 3, 2)->nullable();
                $table->text('maps_url')->nullable();
                $table->boolean('has_website')->default(false);
                $table->text('website_url')->nullable();
                $table->string('location_tag')->nullable();
                $table->string('status')->default('new'); // new, queued_blast, contacted, deal, rejected
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_leads');
        Schema::dropIfExists('scraper_tasks');
    }
};
