<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\GroqAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(
        protected GroqAgentService $groqService
    ) {}

    /**
     * Handle incoming chatbot message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1500',
            'session_id' => 'nullable|string|max:100',
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string',
        ]);

        $sessionId = $validated['session_id'] ?? (string) Str::uuid();
        $ipAddress = $request->header('CF-Connecting-IP')
                    ?? $request->header('X-Forwarded-For')
                    ?? $request->ip();

        if (str_contains($ipAddress, ',')) {
            $ipAddress = explode(',', $ipAddress)[0];
        }

        // Simpan / dapatkan session chat
        $chatSession = ChatSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => trim($ipAddress),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'last_activity_at' => now(),
            ]
        );

        $chatSession->update(['last_activity_at' => now()]);

        // Simpan pesan dari pengguna
        ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'role' => 'user',
            'content' => $validated['message'],
        ]);

        $history = $validated['history'] ?? [];
        $history[] = [
            'role' => 'user',
            'content' => $validated['message'],
        ];

        // Batasi riwayat maksimal 10 pertukaran pesan terakhir untuk menghemat token dan menjaga konteks
        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        $result = $this->groqService->chat($history);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Mohon maaf, saat ini sedang ada kendala teknis pada sistem kami. Silakan coba beberapa saat lagi atau hubungi tim kami via WhatsApp.',
            ], 500);
        }

        // Simpan balasan bot ke riwayat
        ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'role' => 'assistant',
            'content' => $result['reply'],
        ]);

        return response()->json([
            'success' => true,
            'reply' => $result['reply'],
            'session_id' => $sessionId,
        ]);
    }
}
