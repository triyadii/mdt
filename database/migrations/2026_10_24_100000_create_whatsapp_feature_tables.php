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
        // Table for storing WhatsApp Chats/Contacts
        if (! Schema::hasTable('wa_chats')) {
            Schema::create('wa_chats', function (Blueprint $table) {
                $table->id();
                $table->string('number')->unique();
                $table->string('name')->nullable();
                $table->text('last_message')->nullable();
                $table->timestamp('last_message_at')->nullable();
                $table->integer('unread_count')->default(0);
                $table->timestamps();
            });
        }

        // Table for storing WhatsApp Messages
        if (! Schema::hasTable('wa_messages')) {
            Schema::create('wa_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wa_chat_id')->constrained('wa_chats')->onDelete('cascade');
                $table->string('number');
                $table->text('message');
                $table->enum('direction', ['outgoing', 'incoming'])->default('outgoing');
                $table->enum('status', ['pending', 'sent', 'failed'])->default('sent');
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }

        // Table for WA Blast Campaigns
        if (! Schema::hasTable('wa_blast_campaigns')) {
            Schema::create('wa_blast_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('message');
                $table->integer('total_recipients')->default(0);
                $table->integer('sent_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->string('status')->default('draft'); // draft, processing, completed, cancelled
                $table->integer('delay_seconds')->default(3);
                $table->uuid('created_by')->nullable();
                $table->foreign('created_by')->references('uuid')->on('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Table for WA Blast Recipients
        if (! Schema::hasTable('wa_blast_recipients')) {
            Schema::create('wa_blast_recipients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained('wa_blast_campaigns')->onDelete('cascade');
                $table->string('recipient_name')->nullable();
                $table->string('phone_number');
                $table->string('status')->default('pending'); // pending, sent, failed
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_blast_recipients');
        Schema::dropIfExists('wa_blast_campaigns');
        Schema::dropIfExists('wa_messages');
        Schema::dropIfExists('wa_chats');
    }
};
