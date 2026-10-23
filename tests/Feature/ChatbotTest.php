<?php

namespace Tests\Feature;

use App\Models\Permohonan;
use App\Services\GroqAgentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_requires_message(): void
    {
        $response = $this->postJson(route('chatbot.message'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_chatbot_endpoint_returns_success(): void
    {
        $this->mock(GroqAgentService::class, function ($mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->andReturn([
                    'success' => true,
                    'reply' => 'Halo! Ini adalah jawaban mock untuk pengujian.',
                ]);
        });

        $response = $this->postJson(route('chatbot.message'), [
            'message' => 'Halo, apa saja layanan di MDT?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'reply' => 'Halo! Ini adalah jawaban mock untuk pengujian.',
            ]);
    }

    public function test_telegram_webhook_callback_approves_meeting(): void
    {
        $permohonan = Permohonan::create([
            'namaPermohonan' => 'Klien Uji Coba',
            'nomorTelepon' => '081234567890',
            'jenisJasa' => 'Aplikasi Web',
            'tanggal_konsultasi' => '2026-10-28',
            'jam_konsultasi' => '10:00',
            'status' => 'pending',
        ]);

        $payload = [
            'callback_query' => [
                'id' => 'cb_12345',
                'data' => "approve_meeting:{$permohonan->id}",
                'message' => [
                    'message_id' => 999,
                    'chat' => [
                        'id' => 5286179681,
                    ],
                ],
            ],
        ];

        $response = $this->postJson(route('telegram.webhook'), $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertEquals('approved', $permohonan->fresh()->status);
    }
}
