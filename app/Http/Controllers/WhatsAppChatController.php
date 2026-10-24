<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\WaChat;
use App\Models\WaMessage;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppChatController extends Controller
{
    public function __construct(
        protected WhatsAppService $waService
    ) {}

    /**
     * Display the WhatsApp Web style Chat interface
     */
    public function index(Request $request): View|RedirectResponse
    {
        // If arrived via an unintentional GET query string from form submission, auto-process and clean redirect
        if ($request->has('_token') && $request->has('number')) {
            if ($request->filled('message')) {
                $formattedNumber = $this->waService->formatPhoneNumber($request->input('number'));
                if (strlen($formattedNumber) >= 8) {
                    $chat = WaChat::firstOrCreate(
                        ['number' => $formattedNumber],
                        [
                            'name' => $request->input('name') ?: null,
                            'last_message' => $request->input('message'),
                            'last_message_at' => now(),
                        ]
                    );

                    if ($request->filled('name') && empty($chat->name)) {
                        $chat->update(['name' => $request->input('name')]);
                    }

                    $apiResult = $this->waService->sendMessage($formattedNumber, $request->input('message'));
                    WaMessage::create([
                        'wa_chat_id' => $chat->id,
                        'number' => $formattedNumber,
                        'message' => $request->input('message'),
                        'direction' => 'outgoing',
                        'status' => $apiResult['success'] ? 'sent' : 'failed',
                        'error_message' => $apiResult['success'] ? null : ($apiResult['message'] ?? 'Gagal mengirim pesan'),
                    ]);

                    $chat->update([
                        'last_message' => $request->input('message'),
                        'last_message_at' => now(),
                    ]);
                }
            }

            return redirect()->route('wa-chat.index');
        }

        $status = $this->waService->getStatus();

        // Get local chats
        $localChats = WaChat::with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }])->orderByDesc('last_message_at')->orderByDesc('updated_at')->get();

        // Get contacts from Permohonan for quick autocomplete
        $permohonanContacts = Permohonan::select('namaPermohonan', 'nomorTelepon', 'jenisJasa')
            ->whereNotNull('nomorTelepon')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('admin.wa_chat.index', compact('status', 'localChats', 'permohonanContacts'));
    }

    /**
     * Get real-time connection status
     */
    public function status(): JsonResponse
    {
        $status = $this->waService->getStatus();

        return response()->json($status);
    }

    /**
     * Start/connect WhatsApp session
     */
    public function startSession(): JsonResponse
    {
        $result = $this->waService->start();

        return response()->json($result);
    }

    /**
     * Get list of chats (combining gateway & local database)
     */
    public function chats(): JsonResponse
    {
        $localChats = WaChat::orderByDesc('last_message_at')->orderByDesc('updated_at')->get();

        // Also check if gateway returns any additional chats
        $gatewayChats = $this->waService->getChats();
        $chatsList = [];

        // Track existing numbers
        $knownNumbers = [];

        foreach ($localChats as $chat) {
            $knownNumbers[$chat->number] = true;
            $chatsList[] = [
                'id' => $chat->id,
                'number' => $chat->number,
                'name' => $chat->name ?: $chat->number,
                'last_message' => $chat->last_message ?: 'Belum ada pesan',
                'last_message_time' => $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '',
                'unread_count' => $chat->unread_count,
            ];
        }

        // Merge gateway chats if available
        if ($gatewayChats['success'] && ! empty($gatewayChats['chats'])) {
            foreach ($gatewayChats['chats'] as $gChat) {
                $rawNumber = is_array($gChat) ? ($gChat['id'] ?? $gChat['jid'] ?? '') : '';
                $cleanNumber = preg_replace('/[^0-9]/', '', explode('@', $rawNumber)[0]);

                if ($cleanNumber && ! isset($knownNumbers[$cleanNumber])) {
                    $knownNumbers[$cleanNumber] = true;
                    $chatsList[] = [
                        'id' => 'gw_'.$cleanNumber,
                        'number' => $cleanNumber,
                        'name' => $gChat['name'] ?? $cleanNumber,
                        'last_message' => $gChat['lastMessage'] ?? $gChat['message'] ?? 'Pesan WhatsApp',
                        'last_message_time' => isset($gChat['timestamp']) ? Carbon::createFromTimestamp($gChat['timestamp'])->diffForHumans() : '',
                        'unread_count' => $gChat['unreadCount'] ?? 0,
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'chats' => $chatsList,
        ]);
    }

    /**
     * Get conversation messages for a chat
     */
    public function messages(Request $request, string $id): JsonResponse
    {
        $chat = null;
        if (str_starts_with($id, 'gw_')) {
            $number = substr($id, 3);
            $chat = WaChat::where('number', $number)->first();
        } elseif (is_numeric($id)) {
            $chat = WaChat::find($id);
            if (! $chat) {
                $chat = WaChat::where('number', $id)->first();
            }
        } else {
            $chat = WaChat::where('number', $id)->first();
        }

        $messages = [];

        if ($chat) {
            // Reset unread count
            if ($chat->unread_count > 0) {
                $chat->update(['unread_count' => 0]);
            }

            $localMessages = $chat->messages()->orderBy('created_at', 'asc')->get();
            foreach ($localMessages as $msg) {
                $messages[] = [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'direction' => $msg->direction,
                    'status' => $msg->status,
                    'time' => $msg->created_at->format('H:i'),
                    'date' => $msg->created_at->format('d M Y'),
                ];
            }
        }

        // If local is empty, try gateway
        if (empty($messages) && $chat) {
            $gwMessages = $this->waService->getChatMessages($chat->number);
            if ($gwMessages['success'] && ! empty($gwMessages['messages'])) {
                foreach ($gwMessages['messages'] as $gMsg) {
                    $messages[] = [
                        'id' => $gMsg['id'] ?? uniqid(),
                        'message' => $gMsg['message'] ?? $gMsg['body'] ?? '',
                        'direction' => ($gMsg['fromMe'] ?? false) ? 'outgoing' : 'incoming',
                        'status' => 'sent',
                        'time' => isset($gMsg['timestamp']) ? Carbon::createFromTimestamp($gMsg['timestamp'])->format('H:i') : '',
                        'date' => isset($gMsg['timestamp']) ? Carbon::createFromTimestamp($gMsg['timestamp'])->format('d M Y') : '',
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'chat' => $chat ? [
                'id' => $chat->id,
                'number' => $chat->number,
                'name' => $chat->name ?: $chat->number,
            ] : null,
            'messages' => $messages,
        ]);
    }

    /**
     * Send WhatsApp message & create or update chat record
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'number' => 'required|string',
            'message' => 'required|string',
            'name' => 'nullable|string',
        ]);

        $formattedNumber = $this->waService->formatPhoneNumber($validated['number']);

        if (strlen($formattedNumber) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp tidak valid. Masukkan nomor yang benar (contoh: 08123456789 atau 628123456789).',
            ], 422);
        }

        // Find or create Chat contact
        $chat = WaChat::firstOrCreate(
            ['number' => $formattedNumber],
            [
                'name' => $validated['name'] ?: null,
                'last_message' => $validated['message'],
                'last_message_at' => now(),
            ]
        );

        if (! empty($validated['name']) && empty($chat->name)) {
            $chat->update(['name' => $validated['name']]);
        }

        // Send via WhatsApp Gateway
        $apiResult = $this->waService->sendMessage($formattedNumber, $validated['message']);

        $status = $apiResult['success'] ? 'sent' : 'failed';
        $errorMessage = $apiResult['success'] ? null : ($apiResult['message'] ?? 'Gagal mengirim pesan');

        // Store message record
        $message = WaMessage::create([
            'wa_chat_id' => $chat->id,
            'number' => $formattedNumber,
            'message' => $validated['message'],
            'direction' => 'outgoing',
            'status' => $status,
            'error_message' => $errorMessage,
        ]);

        // Update chat
        $chat->update([
            'last_message' => $validated['message'],
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => $apiResult['success'],
            'message' => $apiResult['success'] ? 'Pesan berhasil dikirim!' : ($errorMessage ?: 'Gagal mengirim pesan melalui gateway WhatsApp.'),
            'chat' => [
                'id' => $chat->id,
                'number' => $chat->number,
                'name' => $chat->name ?: $chat->number,
            ],
            'sent_message' => [
                'id' => $message->id,
                'message' => $message->message,
                'direction' => $message->direction,
                'status' => $message->status,
                'time' => $message->created_at->format('H:i'),
                'date' => $message->created_at->format('d M Y'),
                'error' => $errorMessage,
            ],
        ]);
    }

    /**
     * Delete entire conversation by Chat ID or Phone Number
     */
    public function destroyChat(string $id): JsonResponse
    {
        $chat = null;
        if (str_starts_with($id, 'gw_')) {
            $number = substr($id, 3);
            $chat = WaChat::where('number', $number)->first();
        } elseif (is_numeric($id)) {
            $chat = WaChat::find($id);
            if (! $chat) {
                $chat = WaChat::where('number', $id)->first();
            }
        } else {
            $chat = WaChat::where('number', $id)->first();
        }

        $number = $chat ? $chat->number : (str_starts_with($id, 'gw_') ? substr($id, 3) : $id);

        if ($chat) {
            $chat->messages()->delete();
            $chat->delete();
        }

        // Attempt gateway chat deletion if endpoint is available
        $this->waService->deleteChat($number);

        return response()->json([
            'success' => true,
            'message' => 'Obrolan berhasil dihapus.',
        ]);
    }

    /**
     * Delete a single message from conversation
     */
    public function destroyMessage(int $id): JsonResponse
    {
        $message = WaMessage::find($id);
        if (! $message) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak ditemukan.',
            ], 404);
        }

        $chat = $message->chat;
        $chatId = $message->wa_chat_id;
        $number = $message->number;

        $message->delete();

        // Attempt gateway message deletion/revoke
        $this->waService->deleteMessage($number, (string) $id);

        // Update last message in chat
        if ($chat) {
            $latestMessage = WaMessage::where('wa_chat_id', $chatId)->latest()->first();
            $chat->update([
                'last_message' => $latestMessage ? $latestMessage->message : null,
                'last_message_at' => $latestMessage ? $latestMessage->created_at : null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus.',
            'last_message' => $chat && $chat->last_message ? $chat->last_message : 'Belum ada percakapan',
        ]);
    }
}
