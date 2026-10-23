<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected TelegramService $telegramService
    ) {}

    /**
     * Handle incoming webhook updates from Telegram Bot
     */
    public function handle(Request $request): JsonResponse
    {
        $update = $request->all();

        // Check if update is a callback_query (button press)
        if (isset($update['callback_query'])) {
            $callbackQuery = $update['callback_query'];
            $callbackId = $callbackQuery['id'];
            $data = $callbackQuery['data'] ?? '';
            $chatId = (string) ($callbackQuery['message']['chat']['id'] ?? '');
            $messageId = (int) ($callbackQuery['message']['message_id'] ?? 0);

            if (str_starts_with($data, 'approve_meeting:') || str_starts_with($data, 'reject_meeting:')) {
                [$action, $permohonanId] = explode(':', $data, 2);
                $permohonan = Permohonan::find($permohonanId);

                if (! $permohonan) {
                    $this->telegramService->answerCallbackQuery($callbackId, 'Data permohonan tidak ditemukan.', true);

                    return response()->json(['status' => 'not_found']);
                }

                $nowFormatted = now()->timezone('Asia/Jakarta')->format('d M Y H:i').' WIB';
                $tanggal = $permohonan->tanggal_konsultasi ? date('d F Y', strtotime($permohonan->tanggal_konsultasi)) : 'Belum ditentukan';
                $jam = $permohonan->jam_konsultasi ? e($permohonan->jam_konsultasi).' WIB' : 'Fleksibel';
                $nama = e($permohonan->namaPermohonan);
                $telepon = e($permohonan->nomorTelepon);
                $jasa = e($permohonan->jenisJasa ?? 'Konsultasi Umum');
                $keterangan = e($permohonan->keteranganPermohonan ?? '-');

                if ($action === 'approve_meeting') {
                    $permohonan->update(['status' => 'approved']);
                    $this->telegramService->answerCallbackQuery($callbackId, 'Jadwal konsultasi telah disetujui! ✅');

                    $updatedText = "✅ <b>JADWAL KONSULTASI TELAH DISETUJUI</b>\n\n";
                    $updatedText .= "👤 <b>Klien:</b> {$nama}\n";
                    $updatedText .= '📱 <b>No. HP / WA:</b> <a href="https://wa.me/'.preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $telepon))."\">{$telepon}</a>\n";
                    $updatedText .= "💼 <b>Kebutuhan:</b> {$jasa}\n";
                    $updatedText .= "🗓 <b>Tanggal:</b> {$tanggal}\n";
                    $updatedText .= "⏰ <b>Waktu:</b> {$jam}\n";
                    $updatedText .= "📝 <b>Catatan:</b> {$keterangan}\n\n";
                    $updatedText .= "✨ <b>Status:</b> <b>DISETUJUI</b> (pada {$nowFormatted})\n";
                    $updatedText .= '<i>Silakan hubungi calon klien via WhatsApp untuk konfirmasi link Google Meet / lokasi.</i>';

                    // Remove buttons by passing empty keyboard or null
                    $this->telegramService->editMessageText($chatId, $messageId, $updatedText, []);
                } else {
                    $permohonan->update(['status' => 'rejected']);
                    $this->telegramService->answerCallbackQuery($callbackId, 'Jadwal konsultasi ditolak. ❌');

                    $updatedText = "❌ <b>JADWAL KONSULTASI DITOLAK</b>\n\n";
                    $updatedText .= "👤 <b>Klien:</b> {$nama}\n";
                    $updatedText .= "📱 <b>No. HP / WA:</b> {$telepon}\n";
                    $updatedText .= "💼 <b>Kebutuhan:</b> {$jasa}\n";
                    $updatedText .= "🗓 <b>Tanggal:</b> {$tanggal}\n";
                    $updatedText .= "⏰ <b>Waktu:</b> {$jam}\n";
                    $updatedText .= "📝 <b>Catatan:</b> {$keterangan}\n\n";
                    $updatedText .= "⚠️ <b>Status:</b> <b>DITOLAK</b> (pada {$nowFormatted})";

                    $this->telegramService->editMessageText($chatId, $messageId, $updatedText, []);
                }

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'ignored']);
    }
}
