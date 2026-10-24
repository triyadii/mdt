<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;

    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.wa_gateway.base_url', 'https://mdt-solution.site/wa-gateway');
        $this->token = config('services.wa_gateway.token', '');
    }

    /**
     * Standardize Indonesian phone numbers to 62...
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Strip non-digits
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Replace leading 0 with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        // If starts with 8, prepend 62
        if (str_starts_with($phone, '8')) {
            $phone = '62'.$phone;
        }

        return $phone;
    }

    /**
     * Common HTTP client with auth headers
     */
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'x-api-key' => $this->token,
            'Accept' => 'application/json',
        ])->timeout(15);
    }

    /**
     * Get WhatsApp gateway status
     */
    public function getStatus(): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/api/v1/wa/status");

            return [
                'success' => $response->successful(),
                'http_code' => $response->status(),
                'data' => $response->json() ?? [],
                'status' => $response->json('status', 'unknown'),
                'qr' => $response->json('qr'),
                'sessionId' => $response->json('sessionId'),
                'message' => $response->json('message') ?? $response->json('error'),
            ];
        } catch (\Throwable $e) {
            Log::error('WA Gateway getStatus failed: '.$e->getMessage());

            return [
                'success' => false,
                'http_code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Start/initialize session
     */
    public function start(): array
    {
        try {
            $response = $this->client()->asJson()->post("{$this->baseUrl}/api/v1/wa/start", []);

            return [
                'success' => $response->successful(),
                'http_code' => $response->status(),
                'data' => $response->json() ?? [],
                'status' => $response->json('status', 'unknown'),
                'sessionId' => $response->json('sessionId'),
                'message' => $response->json('message') ?? $response->json('error'),
            ];
        } catch (\Throwable $e) {
            Log::error('WA Gateway start failed: '.$e->getMessage());

            return [
                'success' => false,
                'http_code' => 500,
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Send WhatsApp Message
     */
    public function sendMessage(string $number, string $message): array
    {
        $formattedNumber = $this->formatPhoneNumber($number);

        try {
            $response = $this->client()->asJson()->post("{$this->baseUrl}/api/v1/wa/send", [
                'number' => $formattedNumber,
                'message' => $message,
            ]);

            return [
                'success' => $response->successful(),
                'http_code' => $response->status(),
                'data' => $response->json() ?? [],
                'message' => $response->json('message') ?? $response->json('error') ?? ($response->successful() ? 'Pesan berhasil dikirim' : 'Gagal mengirim pesan'),
            ];
        } catch (\Throwable $e) {
            Log::error('WA Gateway sendMessage failed: '.$e->getMessage());

            return [
                'success' => false,
                'http_code' => 500,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Get Chat list
     */
    public function getChats(): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/api/v1/wa/chats");

            if ($response->successful()) {
                $payload = $response->json();

                return [
                    'success' => true,
                    'chats' => $payload['data'] ?? (is_array($payload) && isset($payload[0]) ? $payload : []),
                ];
            }

            return [
                'success' => false,
                'http_code' => $response->status(),
                'chats' => [],
                'message' => $response->json('message') ?? $response->json('error') ?? 'Gagal mengambil daftar chat',
            ];
        } catch (\Throwable $e) {
            Log::error('WA Gateway getChats failed: '.$e->getMessage());

            return [
                'success' => false,
                'chats' => [],
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Chat Messages by Chat ID / Phone Number
     */
    public function getChatMessages(string $chatId): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/api/v1/wa/chats/{$chatId}");

            if ($response->successful()) {
                $payload = $response->json();

                return [
                    'success' => true,
                    'messages' => $payload['data'] ?? (is_array($payload) && isset($payload[0]) ? $payload : []),
                ];
            }

            return [
                'success' => false,
                'http_code' => $response->status(),
                'messages' => [],
                'message' => $response->json('message') ?? $response->json('error') ?? 'Gagal mengambil riwayat pesan',
            ];
        } catch (\Throwable $e) {
            Log::error('WA Gateway getChatMessages failed: '.$e->getMessage());

            return [
                'success' => false,
                'messages' => [],
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete Chat on Gateway (if supported)
     */
    public function deleteChat(string $chatId): array
    {
        try {
            $response = $this->client()->delete("{$this->baseUrl}/api/v1/wa/chats/{$chatId}");

            return [
                'success' => $response->successful(),
                'http_code' => $response->status(),
                'data' => $response->json() ?? [],
                'message' => $response->json('message') ?? $response->json('error'),
            ];
        } catch (\Throwable $e) {
            Log::info('WA Gateway deleteChat attempted: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete / Revoke Message on Gateway (if supported)
     */
    public function deleteMessage(string $chatId, string $messageId): array
    {
        try {
            $response = $this->client()->delete("{$this->baseUrl}/api/v1/wa/messages/{$messageId}", [
                'chatId' => $chatId,
            ]);

            return [
                'success' => $response->successful(),
                'http_code' => $response->status(),
                'data' => $response->json() ?? [],
                'message' => $response->json('message') ?? $response->json('error'),
            ];
        } catch (\Throwable $e) {
            Log::info('WA Gateway deleteMessage attempted: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
