<?php

namespace App\Http\Controllers;

use App\Services\GroqAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string',
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
                'message' => $result['message'] ?? 'Gagal memproses pesan.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'reply' => $result['reply'],
        ]);
    }
}
