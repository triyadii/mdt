<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\WaBlastCampaign;
use App\Models\WaBlastRecipient;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WhatsAppBlastController extends Controller
{
    public function __construct(
        protected WhatsAppService $waService
    ) {}

    /**
     * Display list of WhatsApp Blast campaigns & summary
     */
    public function index(): View
    {
        $campaigns = WaBlastCampaign::with('creator')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'total_campaigns' => WaBlastCampaign::count(),
            'total_recipients' => WaBlastCampaign::sum('total_recipients'),
            'total_sent' => WaBlastCampaign::sum('sent_count'),
            'total_failed' => WaBlastCampaign::sum('failed_count'),
        ];

        return view('admin.wa_blast.index', compact('campaigns', 'stats'));
    }

    /**
     * Show form to create new blast campaign
     */
    public function create(): View
    {
        $totalPermohonan = Permohonan::whereNotNull('nomorTelepon')->count();

        return view('admin.wa_blast.create', compact('totalPermohonan'));
    }

    /**
     * Store new blast campaign & parse recipient numbers
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'delay_seconds' => 'required|integer|min:1|max:60',
            'recipients_manual' => 'nullable|string',
            'import_permohonan' => 'nullable|boolean',
            'file_recipients' => 'nullable|file|mimes:txt,csv|max:2048',
        ]);

        $collected = []; // [number => name]

        // 1. Parse manual input
        if (! empty($request->recipients_manual)) {
            $lines = preg_split('/\r\n|\r|\n/', $request->recipients_manual);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                // Check if format is "Name, Number" or "Number"
                if (str_contains($line, ',')) {
                    $parts = explode(',', $line, 2);
                    $name = trim($parts[0]);
                    $rawNum = trim($parts[1]);
                } else {
                    $name = '';
                    $rawNum = $line;
                }

                $phone = $this->waService->formatPhoneNumber($rawNum);
                if (strlen($phone) >= 8) {
                    $collected[$phone] = $name ?: ($collected[$phone] ?? '');
                }
            }
        }

        // 2. Parse uploaded file
        if ($request->hasFile('file_recipients')) {
            $file = $request->file('file_recipients');
            $content = file_get_contents($file->getRealPath());
            $lines = preg_split('/\r\n|\r|\n/', $content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                if (str_contains($line, ',')) {
                    $parts = explode(',', $line, 2);
                    $name = trim($parts[0]);
                    $rawNum = trim($parts[1]);
                } else {
                    $name = '';
                    $rawNum = $line;
                }

                $phone = $this->waService->formatPhoneNumber($rawNum);
                if (strlen($phone) >= 8) {
                    $collected[$phone] = $name ?: ($collected[$phone] ?? '');
                }
            }
        }

        // 3. Import from Permohonan
        if ($request->boolean('import_permohonan')) {
            $permohonans = Permohonan::whereNotNull('nomorTelepon')->get();
            foreach ($permohonans as $p) {
                $phone = $this->waService->formatPhoneNumber($p->nomorTelepon);
                if (strlen($phone) >= 8) {
                    if (! isset($collected[$phone]) || empty($collected[$phone])) {
                        $collected[$phone] = $p->namaPermohonan ?: '';
                    }
                }
            }
        }

        if (empty($collected)) {
            return back()->withInput()->with('error', 'Tidak ada nomor penerima yang valid. Silakan masukkan nomor tujuan atau centang opsi impor.');
        }

        // Create campaign
        $campaign = DB::transaction(function () use ($request, $collected) {
            $user = Auth::user();

            $campaign = WaBlastCampaign::create([
                'title' => $request->title,
                'message' => $request->message,
                'total_recipients' => count($collected),
                'sent_count' => 0,
                'failed_count' => 0,
                'status' => 'draft',
                'delay_seconds' => $request->delay_seconds,
                'created_by' => $user ? $user->uuid : null,
            ]);

            $recipientsData = [];
            $now = now();
            foreach ($collected as $phone => $name) {
                $recipientsData[] = [
                    'campaign_id' => $campaign->id,
                    'recipient_name' => $name ?: null,
                    'phone_number' => $phone,
                    'status' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Chunk insert for database performance
            foreach (array_chunk($recipientsData, 500) as $chunk) {
                WaBlastRecipient::insert($chunk);
            }

            return $campaign;
        });

        return redirect()->route('wa-blast.show', $campaign->id)
            ->with('success', 'Kampanye promosi berhasil dibuat dengan '.count($collected).' nomor target.');
    }

    /**
     * Show campaign details & execution interface
     */
    public function show(int $id): View
    {
        $campaign = WaBlastCampaign::with(['recipients' => function ($q) {
            $q->orderBy('status', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($id);

        $status = $this->waService->getStatus();

        return view('admin.wa_blast.show', compact('campaign', 'status'));
    }

    /**
     * Send message to a single recipient (called iteratively via AJAX by the frontend)
     */
    public function sendSingle(Request $request, int $campaignId, int $recipientId): JsonResponse
    {
        $campaign = WaBlastCampaign::findOrFail($campaignId);
        $recipient = WaBlastRecipient::where('campaign_id', $campaignId)->findOrFail($recipientId);

        if ($recipient->status === 'sent') {
            return response()->json([
                'success' => true,
                'already_sent' => true,
                'campaign' => $campaign,
                'recipient' => $recipient,
            ]);
        }

        // Update campaign status to processing
        if ($campaign->status === 'draft') {
            $campaign->update(['status' => 'processing']);
        }

        // Personalized message placeholders
        $personalizedMessage = str_replace(
            ['{nama}', '{nomor}', '{tanggal}'],
            [
                $recipient->recipient_name ?: 'Pelanggan Setia',
                $recipient->phone_number,
                Carbon::now()->translatedFormat('d F Y'),
            ],
            $campaign->message
        );

        // Send via WhatsApp Gateway
        $apiResult = $this->waService->sendMessage($recipient->phone_number, $personalizedMessage);

        if ($apiResult['success']) {
            $recipient->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            $campaign->increment('sent_count');
        } else {
            $recipient->update([
                'status' => 'failed',
                'sent_at' => now(),
                'error_message' => $apiResult['message'] ?? 'Gagal mengirim pesan',
            ]);

            $campaign->increment('failed_count');
        }

        $campaign->refresh();

        // Check if all recipients processed
        $remainingPending = WaBlastRecipient::where('campaign_id', $campaignId)
            ->where('status', 'pending')
            ->count();

        if ($remainingPending === 0) {
            $campaign->update(['status' => 'completed']);
        }

        return response()->json([
            'success' => $apiResult['success'],
            'message' => $apiResult['message'],
            'campaign' => [
                'id' => $campaign->id,
                'status' => $campaign->status,
                'total_recipients' => $campaign->total_recipients,
                'sent_count' => $campaign->sent_count,
                'failed_count' => $campaign->failed_count,
                'remaining' => $remainingPending,
            ],
            'recipient' => [
                'id' => $recipient->id,
                'phone_number' => $recipient->phone_number,
                'recipient_name' => $recipient->recipient_name,
                'status' => $recipient->status,
                'sent_at' => $recipient->sent_at ? $recipient->sent_at->format('H:i:s') : null,
                'error_message' => $recipient->error_message,
            ],
        ]);
    }

    /**
     * Delete campaign
     */
    public function destroy(int $id): RedirectResponse
    {
        $campaign = WaBlastCampaign::findOrFail($id);
        $campaign->delete();

        return redirect()->route('wa-blast.index')->with('success', 'Kampanye WA Blast berhasil dihapus.');
    }
}
