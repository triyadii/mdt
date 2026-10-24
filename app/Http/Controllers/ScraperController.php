<?php

namespace App\Http\Controllers;

use App\Models\ScraperTask;
use App\Models\UmkmLead;
use App\Models\WaBlastCampaign;
use App\Models\WaBlastRecipient;
use App\Services\IndonesiaRegionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScraperController extends Controller
{
    /**
     * Dashboard Scraper & Manual Scraping
     */
    public function index(): View
    {
        $tasks = ScraperTask::withCount('leads')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'total_leads' => UmkmLead::count(),
            'total_without_website' => UmkmLead::where('has_website', false)->count(),
            'total_with_wa' => UmkmLead::whereNotNull('phone_wa')->count(),
            'total_tasks' => ScraperTask::count(),
        ];

        $provinces = IndonesiaRegionService::getProvinces();
        $regionsJson = IndonesiaRegionService::getAllRegionsJson();

        return view('admin.scraper.index', compact('tasks', 'stats', 'provinces', 'regionsJson'));
    }

    /**
     * Helper merangkai nama wilayah presisi
     */
    protected function resolveLocation(Request $request): string
    {
        if ($request->filled('location_custom')) {
            return trim($request->location_custom);
        }

        $province = $request->input('province');
        $city = $request->input('city');
        $district = $request->input('district');

        if ($district && $district !== 'Semua Kecamatan') {
            return "{$district}, {$city}, {$province}";
        }

        if ($city) {
            return "{$city}, {$province}";
        }

        return $request->input('location', 'Kota Medan, Sumatera Utara');
    }

    /**
     * Helper merangkai kata kunci kategori pencarian
     */
    protected function resolveKeyword(Request $request): string
    {
        if ($request->input('keyword_preset') === '__custom__' && $request->filled('keyword_custom')) {
            return trim($request->keyword_custom);
        }

        if ($request->filled('keyword') && $request->keyword !== '__custom__') {
            return trim($request->keyword);
        }

        return $request->input('keyword_preset', 'cafe');
    }

    /**
     * Mulai tugas scraping sekarang (background process)
     */
    public function start(Request $request): RedirectResponse
    {
        $request->validate([
            'limit' => 'required|integer|min:1|max:100',
        ]);

        $resolvedKeyword = $this->resolveKeyword($request);
        $resolvedLocation = $this->resolveLocation($request);

        $task = ScraperTask::create([
            'title' => "Scrape {$resolvedKeyword} di {$resolvedLocation}",
            'keyword' => $resolvedKeyword,
            'location' => $resolvedLocation,
            'filter_no_website' => $request->input('filter_no_website', '1') === '1',
            'filter_has_phone' => $request->input('filter_has_phone', '1') === '1',
            'min_reviews' => (int) $request->input('min_reviews', 5),
            'min_rating' => (float) $request->input('min_rating', 0),
            'frequency' => 'once',
            'status' => 'pending',
            'scheduled_at' => now(),
            'log_message' => 'Tugas telah dibuat dan menunggu eksekusi...',
        ]);

        // Jalankan background process via CLI PHP (bukan PHP-FPM)
        $phpBinary = file_exists('/usr/bin/php') ? '/usr/bin/php' : (PHP_BINARY && ! str_contains(PHP_BINARY, 'fpm') ? PHP_BINARY : 'php');
        $artisanPath = base_path('artisan');
        $logFile = "/tmp/scraper_task_{$task->id}.log";
        $command = "nohup {$phpBinary} {$artisanPath} scraper:run {$task->id} --limit={$request->limit} > {$logFile} 2>&1 &";
        exec($command);

        return redirect()->route('scraper.index')->with('success', "Tugas scraping untuk '{$resolvedKeyword}' di '{$resolvedLocation}' berhasil dimulai di background!");
    }

    /**
     * Halaman Manajemen Jadwal Otomatis
     */
    public function schedules(): View
    {
        $schedules = ScraperTask::whereIn('frequency', ['daily', 'weekly'])
            ->orWhere(function ($q) {
                $q->where('frequency', 'once')->where('status', 'pending');
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $provinces = IndonesiaRegionService::getProvinces();
        $regionsJson = IndonesiaRegionService::getAllRegionsJson();

        return view('admin.scraper.schedules', compact('schedules', 'provinces', 'regionsJson'));
    }

    /**
     * Simpan jadwal scraping baru
     */
    public function storeSchedule(Request $request): RedirectResponse
    {
        $request->validate([
            'frequency' => 'required|in:once,daily,weekly',
            'scheduled_at' => 'nullable|date',
        ]);

        $resolvedKeyword = $this->resolveKeyword($request);
        $resolvedLocation = $this->resolveLocation($request);

        ScraperTask::create([
            'title' => "Jadwal: {$resolvedKeyword} di {$resolvedLocation}",
            'keyword' => $resolvedKeyword,
            'location' => $resolvedLocation,
            'filter_no_website' => $request->input('filter_no_website', '1') === '1',
            'filter_has_phone' => $request->input('filter_has_phone', '1') === '1',
            'min_reviews' => (int) $request->input('min_reviews', 5),
            'min_rating' => (float) $request->input('min_rating', 0),
            'frequency' => $request->frequency,
            'status' => 'pending',
            'scheduled_at' => $request->scheduled_at ? $request->scheduled_at : now(),
            'log_message' => "Jadwal dibuat dengan frekuensi: {$request->frequency}",
        ]);

        return redirect()->route('scraper.schedules')->with('success', "Jadwal scraping otomatis '{$resolvedKeyword}' di '{$resolvedLocation}' berhasil disimpan!");
    }

    /**
     * Hapus jadwal / tugas
     */
    public function destroyTask(int $id): RedirectResponse
    {
        $task = ScraperTask::findOrFail($id);
        $task->delete();

        return back()->with('success', 'Tugas/jadwal scraping berhasil dihapus.');
    }

    /**
     * Halaman Daftar Hasil Leads UMKM
     */
    public function leads(Request $request): View
    {
        $query = UmkmLead::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'ilike', "%{$q}%")
                    ->orWhere('phone_wa', 'like', "%{$q}%")
                    ->orWhere('address', 'ilike', "%{$q}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('location_tag', $request->location);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->input('per_page', 20);
        if (! in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 20;
        }

        $leads = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
        $locations = UmkmLead::select('location_tag')->distinct()->pluck('location_tag')->filter();

        return view('admin.scraper.leads', compact('leads', 'locations', 'perPage'));
    }

    /**
     * Kirim/Push Data Leads terpilih ke WA Blast Campaign
     */
    public function pushToWaBlast(Request $request): RedirectResponse
    {
        $request->validate([
            'campaign_title' => 'required|string|max:255',
            'message' => 'required|string',
            'delay_seconds' => 'required|integer|min:2|max:30',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'exists:umkm_leads,id',
            'send_all_new' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            if ($request->boolean('send_all_new')) {
                $leads = UmkmLead::where('status', 'new')->whereNotNull('phone_wa')->get();
            } else {
                $leadIds = $request->input('lead_ids', []);
                if (empty($leadIds)) {
                    return back()->with('error', 'Pilih minimal satu lead UMKM untuk dimasukkan ke WA Blast.');
                }
                $leads = UmkmLead::whereIn('id', $leadIds)->whereNotNull('phone_wa')->get();
            }

            if ($leads->isEmpty()) {
                return back()->with('error', 'Tidak ada lead dengan nomor WhatsApp valid yang dapat dimasukkan.');
            }

            // Buat Campaign WA Blast baru
            $campaign = WaBlastCampaign::create([
                'title' => $request->campaign_title,
                'message' => $request->message,
                'total_recipients' => $leads->count(),
                'sent_count' => 0,
                'failed_count' => 0,
                'status' => 'draft',
                'delay_seconds' => $request->delay_seconds,
                'created_by' => Auth::user()?->uuid,
            ]);

            // Masukkan setiap lead ke recipients
            foreach ($leads as $lead) {
                WaBlastRecipient::create([
                    'campaign_id' => $campaign->id,
                    'recipient_name' => $lead->name,
                    'phone_number' => $lead->phone_wa,
                    'status' => 'pending',
                ]);

                // Update status lead
                $lead->update(['status' => 'queued_blast']);
            }

            DB::commit();

            return redirect()->route('wa-blast.show', $campaign->id)->with('success', "Berhasil membuat Campaign WA Blast dengan {$leads->count()} kontak UMKM dari Google Maps!");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses ke WA Blast: '.$e->getMessage());
        }
    }

    /**
     * Cek status tugas & log lengkap (AJAX polling)
     */
    public function taskStatus(int $id): JsonResponse
    {
        $task = ScraperTask::findOrFail($id);

        $etaText = '-';
        if ($task->eta_seconds && $task->eta_seconds > 0) {
            $mins = floor($task->eta_seconds / 60);
            $secs = $task->eta_seconds % 60;
            $etaText = $mins > 0 ? "± {$mins}m {$secs}s" : "± {$secs} detik";
        }

        return response()->json([
            'id' => $task->id,
            'title' => $task->title,
            'keyword' => $task->keyword,
            'location' => $task->location,
            'status' => $task->status,
            'leads_count' => $task->leads_count,
            'progress_percent' => $task->progress_percent ?? 0,
            'current_step' => $task->current_step ?? 'Menyiapkan browser...',
            'eta_seconds' => $task->eta_seconds,
            'eta_text' => $etaText,
            'log_message' => $task->log_message,
            'last_run_at' => $task->last_run_at?->format('d/m/Y H:i:s'),
            'created_at' => $task->created_at?->format('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Hapus lead
     */
    public function destroyLead(int $id): RedirectResponse
    {
        $lead = UmkmLead::findOrFail($id);
        $lead->delete();

        return back()->with('success', 'Data lead berhasil dihapus.');
    }

    /**
     * Hapus banyak data lead sekaligus (Bulk Delete)
     */
    public function bulkDeleteLeads(Request $request): RedirectResponse
    {
        $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'exists:umkm_leads,id',
        ], [
            'lead_ids.required' => 'Pilih minimal satu data lead UMKM untuk dihapus.',
            'lead_ids.min' => 'Pilih minimal satu data lead UMKM untuk dihapus.',
        ]);

        $count = UmkmLead::whereIn('id', $request->lead_ids)->delete();

        return back()->with('success', "Berhasil menghapus {$count} data lead UMKM terpilih.");
    }
}
