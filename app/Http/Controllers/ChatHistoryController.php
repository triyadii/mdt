<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatHistoryController extends Controller
{
    /**
     * Display a listing of chatbot conversations/sessions.
     */
    public function index(Request $request): View
    {
        $query = ChatSession::withCount('messages')
            ->with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('session_id', 'like', "%{$search}%")
                    ->orWhereHas('messages', function ($mq) use ($search) {
                        $mq->where('content', 'ilike', "%{$search}%");
                    });
            });
        }

        $sessions = $query->orderBy('last_activity_at', 'desc')->paginate(15)->withQueryString();

        return view('chat_histories.index', compact('sessions'));
    }

    /**
     * Display the specified chat session with all messages (for AJAX modal or inspection).
     */
    public function show(int $id, Request $request): JsonResponse|View
    {
        $session = ChatSession::with(['messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'created_at' => $session->created_at?->format('d M Y, H:i:s'),
                    'last_activity_at' => $session->last_activity_at?->format('d M Y, H:i:s'),
                    'messages_count' => $session->messages->count(),
                ],
                'messages' => $session->messages->map(fn ($msg) => [
                    'id' => $msg->id,
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at?->format('d M Y, H:i:s'),
                    'time_ago' => $msg->created_at?->diffForHumans(),
                ]),
            ]);
        }

        return view('chat_histories.show', compact('session'));
    }

    /**
     * Remove the specified chat session and its messages from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $session = ChatSession::findOrFail($id);
        $session->delete();

        return redirect()->route('chat-histories.index')
            ->with('success', 'Riwayat sesi chat berhasil dihapus.');
    }
}
