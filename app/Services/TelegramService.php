<?php

namespace App\Services;

use App\Models\Permohonan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;

    protected ?string $adminChatId;

    protected string $baseUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->adminChatId = config('services.telegram.admin_chat_id');
        $this->baseUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send general message to Telegram chat
     */
    public function sendMessage(string $text, ?array $inlineKeyboard = null, ?string $chatId = null, string $parseMode = 'HTML'): ?array
    {
        $targetChatId = $chatId ?: $this->adminChatId;

        if (empty($this->botToken) || empty($targetChatId)) {
            Log::warning('Telegram token or chat ID is not configured.');

            return null;
        }

        $payload = [
            'chat_id' => $targetChatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if (! empty($inlineKeyboard)) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $inlineKeyboard,
            ]);
        }

        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/sendMessage", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Telegram sendMessage error: '.$response->body());

            return null;
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Send interactive booking notification to Admin with Approve/Reject buttons
     */
    public function sendMeetingBookingNotification(Permohonan $permohonan): ?array
    {
        $tanggal = $permohonan->tanggal_konsultasi ? date('d F Y', strtotime($permohonan->tanggal_konsultasi)) : 'Belum ditentukan';
        $jam = $permohonan->jam_konsultasi ? e($permohonan->jam_konsultasi).' WIB' : 'Fleksibel';
        $nama = e($permohonan->namaPermohonan);
        $telepon = e($permohonan->nomorTelepon);
        $jasa = e($permohonan->jenisJasa ?? 'Konsultasi Umum');
        $keterangan = e($permohonan->keteranganPermohonan ?? '-');

        $message = "📅 <b>PERMOHONAN JADWAL KONSULTASI BARU</b>\n\n";
        $message .= "👤 <b>Klien:</b> {$nama}\n";
        $message .= '📱 <b>No. HP / WA:</b> <a href="https://wa.me/'.preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $telepon))."\">{$telepon}</a>\n";
        $message .= "💼 <b>Kebutuhan:</b> {$jasa}\n";
        $message .= "🗓 <b>Tanggal:</b> {$tanggal}\n";
        $message .= "⏰ <b>Waktu:</b> {$jam}\n";
        $message .= "📝 <b>Catatan:</b> {$keterangan}\n\n";
        $message .= "⏳ <i>Status saat ini: Menunggu Persetujuan</i>\n\n";
        $message .= 'Silakan tentukan persetujuan jadwal di bawah:';

        $keyboard = [
            [
                ['text' => '✅ Setujui Jadwal', 'callback_data' => "approve_meeting:{$permohonan->id}"],
                ['text' => '❌ Tolak Jadwal', 'callback_data' => "reject_meeting:{$permohonan->id}"],
            ],
        ];

        $res = $this->sendMessage($message, $keyboard);

        if (! empty($res['result']['message_id'])) {
            $permohonan->update([
                'telegram_message_id' => (string) $res['result']['message_id'],
            ]);
        }

        return $res;
    }

    /**
     * Send visitor notification to Admin
     */
    public function sendVisitorNotification(string $ipAddress, ?string $page = null): ?array
    {
        $time = now()->timezone('Asia/Jakarta')->format('d M Y H:i:s').' WIB';
        $pageText = $page ? e($page) : 'Beranda';

        $message = "🌐 <b>PENGUNJUNG BARU WEBSITE MDT</b>\n\n";
        $message .= "📍 <b>IP:</b> <code>{$ipAddress}</code>\n";
        $message .= "🕒 <b>Waktu:</b> {$time}\n";
        $message .= "📄 <b>Halaman:</b> {$pageText}\n";

        return $this->sendMessage($message);
    }

    /**
     * Edit message in Telegram
     */
    public function editMessageText(string $chatId, int $messageId, string $text, ?array $inlineKeyboard = null, string $parseMode = 'HTML'): ?array
    {
        if (empty($this->botToken)) {
            return null;
        }

        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if ($inlineKeyboard !== null) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $inlineKeyboard,
            ]);
        }

        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/editMessageText", $payload);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Telegram editMessageText exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Answer Telegram callback query (removes loading state on button click)
     */
    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null, bool $showAlert = false): ?array
    {
        if (empty($this->botToken)) {
            return null;
        }

        $payload = [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ];

        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/answerCallbackQuery", $payload);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Telegram answerCallbackQuery exception: '.$e->getMessage());

            return null;
        }
    }
}
