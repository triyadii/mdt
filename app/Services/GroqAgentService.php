<?php

namespace App\Services;

use App\Models\About;
use App\Models\CompanyProfile;
use App\Models\Faq;
use App\Models\Jasa;
use App\Models\Keunggulan;
use App\Models\Permohonan;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAgentService
{
    protected ?string $apiKey;

    protected string $model;

    protected string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct(
        protected TelegramService $telegramService
    ) {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
    }

    /**
     * Run chat completion with Agentic Tool Calling loop
     */
    public function chat(array $conversationHistory): array
    {
        $friendlyErrorMessage = 'Mohon maaf, saat ini sedang terjadi kendala teknis pada sistem kami. Tim kami sedang menanganinya. Anda juga dapat menghubungi kami langsung melalui WhatsApp untuk respon cepat.';

        if (empty($this->apiKey)) {
            Log::error('Groq API Key belum dikonfigurasi di .env (GROQ_API_KEY).');

            return [
                'success' => false,
                'message' => $friendlyErrorMessage,
            ];
        }

        $today = now()->timezone('Asia/Jakarta');
        $todayStr = $today->format('Y-m-d');
        $dayName = $today->locale('id')->isoFormat('dddd, D MMMM Y');

        $systemPrompt = <<<EOT
Kamu adalah "MDT Assistant", AI Consultant cerdas dan ramah dari MDT Solution Indonesia (PT / Agensi Digital & Solusi Teknologi).
Hari ini adalah {$dayName} (Tanggal: {$todayStr}).

Tugas dan Kemampuanmu:
1. Menjelaskan profil perusahaan, visi, keunggulan, serta kontak resmi MDT Solution Indonesia.
2. Menjelaskan layanan jasa teknologi kami beserta estimasi harga mulai dan durasi pengerjaan.
3. Menjelaskan portofolio atau proyek nyata yang pernah kami kerjakan beserta fitur dan tautannya.
4. Membantu calon klien menjadwalkan meeting konsultasi gratis dengan tim MDT Solution Indonesia.

Panduan Interaksi:
- Gunakan bahasa Indonesia yang santun, ramah, profesional, dan meyakinkan.
- ATURAN TAMPILAN FORMAT (SANGAT PENTING): JANGAN PERNAH membuat atau menampilkan data dalam bentuk TABEL markdown (| Header | Header |), karena tampilan jendela chat sangat ramping sehingga tabel tidak muat dan terpotong.
- SELALU gunakan format DAFTAR / LIST terstruktur (gunakan bullet points •, penomoran 1., emoji seperti 🔹 atau 📌, dan cetak tebal).
  Contoh format list layanan:
  🔹 **Pengembangan Aplikasi Web**
  • Estimasi Harga: Mulai Rp 3.500.000
  • Estimasi Waktu: 1 - 3 Minggu
  • Keterangan: Pembuatan aplikasi web modern dan responsif...
- Selalu gunakan TOOLS yang tersedia untuk mengambil data faktual dari database perusahaan. Jangan mengarang harga atau portofolio.
- Saat calon klien tertarik pada layanan atau ingin diskusi lebih lanjut, ajak dengan sopan untuk menjadwalkan konsultasi meeting gratis.
- Untuk membuat jadwal meeting (tool `book_consultation`), kamu WAJIB menanyakan atau mengonfirmasi minimal 4 data:
  1. Nama Lengkap
  2. Nomor WhatsApp / Telepon
  3. Tanggal Meeting (konversikan jika klien bilang 'besok', 'senin depan', dll ke format YYYY-MM-DD)
  4. Jam Meeting (misal 10:00 atau 14:00)
- Setelah tool `book_consultation` berhasil dieksekusi, beritahu klien dengan ceria bahwa permintaan jadwal meeting telah berhasil diajukan dan sedang menunggu konfirmasi admin via Telegram/WhatsApp, serta tim kami akan segera menghubungi.
EOT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Format history
        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['content'] ?? '',
            ];
        }

        $tools = $this->getToolDefinitions();

        try {
            // Step 1: Call Groq with available tools
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'tools' => $tools,
                    'tool_choice' => 'auto',
                    'temperature' => 0.5,
                ]);

            if (! $response->successful()) {
                Log::error('Groq API Error: '.$response->body());

                return [
                    'success' => false,
                    'message' => $friendlyErrorMessage,
                ];
            }

            $responseData = $response->json();
            $choice = $responseData['choices'][0] ?? null;

            if (! $choice) {
                return [
                    'success' => false,
                    'message' => $friendlyErrorMessage,
                ];
            }

            $assistantMessage = $choice['message'];

            // Step 2: Handle Tool Calls if any
            if (! empty($assistantMessage['tool_calls'])) {
                $messages[] = $assistantMessage;

                foreach ($assistantMessage['tool_calls'] as $toolCall) {
                    $toolName = $toolCall['function']['name'] ?? '';
                    $toolArgs = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?: [];

                    $toolResult = $this->executeTool($toolName, $toolArgs);

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'name' => $toolName,
                        'content' => json_encode($toolResult, JSON_UNESCAPED_UNICODE),
                    ];
                }

                // Step 3: Re-call Groq to formulate the final answer with tool outputs
                $followUpResponse = Http::withToken($this->apiKey)
                    ->timeout(30)
                    ->post($this->apiUrl, [
                        'model' => $this->model,
                        'messages' => $messages,
                        'temperature' => 0.5,
                    ]);

                if (! $followUpResponse->successful()) {
                    Log::error('Groq Follow-up API Error: '.$followUpResponse->body());

                    return [
                        'success' => false,
                        'message' => $friendlyErrorMessage,
                    ];
                }

                $followUpChoice = $followUpResponse->json('choices.0.message');

                return [
                    'success' => true,
                    'reply' => $followUpChoice['content'] ?? 'Selesai.',
                ];
            }

            // Normal text response without tool call
            return [
                'success' => true,
                'reply' => $assistantMessage['content'] ?? '',
            ];
        } catch (\Throwable $e) {
            Log::error('GroqAgentService Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $friendlyErrorMessage,
            ];
        }
    }

    /**
     * Define the tools available to Groq
     */
    protected function getToolDefinitions(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_company_profile',
                    'description' => 'Mengambil informasi lengkap profil perusahaan MDT Solution Indonesia (visi, tentang kami, alamat kantor, email, WhatsApp, media sosial, dan keunggulan utama)',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => new \stdClass,
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_services_and_pricing',
                    'description' => 'Mengambil daftar layanan jasa, estimasi harga mulai, dan estimasi waktu pengerjaan project yang ditawarkan MDT',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'keyword' => [
                                'type' => 'string',
                                'description' => 'Kata kunci nama jasa jika ingin spesifik, misal: "web", "mobile", "sistem", "ui/ux"',
                            ],
                        ],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_portfolio',
                    'description' => 'Mencari portofolio atau proyek yang pernah dikerjakan oleh MDT Solution Indonesia beserta tautan demonya',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Kata kunci proyek yang dicari (misal: "e-commerce", "presensi", "toko online", "aplikasi")',
                            ],
                        ],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_faqs',
                    'description' => 'Mengambil daftar FAQ (pertanyaan umum) seperti lama pengerjaan, termin pembayaran, garansi, dan sistem kerja',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => new \stdClass,
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'book_consultation',
                    'description' => 'Membuat dan mencatat jadwal konsultasi / meeting resmi klien baru ke database dan meneruskan notifikasi persetujuan ke Telegram Admin',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'nama' => [
                                'type' => 'string',
                                'description' => 'Nama lengkap klien / pemohon',
                            ],
                            'nomor_telepon' => [
                                'type' => 'string',
                                'description' => 'Nomor WhatsApp atau HP aktif klien',
                            ],
                            'jenis_jasa' => [
                                'type' => 'string',
                                'description' => 'Layanan yang diminati (contoh: Pengembangan Aplikasi Web, Pembuatan Aplikasi Mobile, dll)',
                            ],
                            'tanggal' => [
                                'type' => 'string',
                                'description' => 'Tanggal meeting dalam format YYYY-MM-DD (contoh: 2026-10-25)',
                            ],
                            'jam' => [
                                'type' => 'string',
                                'description' => 'Waktu / jam meeting dalam format HH:MM (contoh: 10:00 atau 14:00)',
                            ],
                            'keterangan' => [
                                'type' => 'string',
                                'description' => 'Catatan ringkas mengenai kebutuhan proyek atau hal yang ingin didiskusikan',
                            ],
                        ],
                        'required' => ['nama', 'nomor_telepon', 'tanggal', 'jam'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Execute corresponding database query or action for a tool
     */
    protected function executeTool(string $toolName, array $args): array
    {
        return match ($toolName) {
            'get_company_profile' => $this->toolGetCompanyProfile(),
            'get_services_and_pricing' => $this->toolGetServicesAndPricing($args['keyword'] ?? null),
            'search_portfolio' => $this->toolSearchPortfolio($args['query'] ?? null),
            'get_faqs' => $this->toolGetFaqs(),
            'book_consultation' => $this->toolBookConsultation($args),
            default => ['error' => "Tool {$toolName} tidak dikenal."],
        };
    }

    protected function toolGetCompanyProfile(): array
    {
        $company = CompanyProfile::first();
        $about = About::first();
        $keunggulans = Keunggulan::all(['namaUnggulan', 'keteranganUnggulan'])->toArray();

        return [
            'nama_perusahaan' => $company->nama_profil ?? 'MDT Solution Indonesia',
            'tentang' => $about->keterangan_tentang ?? 'Agensi penyedia solusi digital dan transformasi teknologi.',
            'alamat' => $company->alamat ?? 'Jakarta Selatan',
            'email' => $company->email ?? 'halo@mdtkreatif.com',
            'telepon' => $company->nomor_telepon ?? '08111222333',
            'instagram' => $company->instagram ?? '',
            'tiktok' => $company->tiktok ?? '',
            'keunggulan' => $keunggulans,
        ];
    }

    protected function toolGetServicesAndPricing(?string $keyword): array
    {
        $query = Jasa::query();

        if (! empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('namaJasa', 'ilike', "%{$keyword}%")
                    ->orWhere('keteranganJasa', 'ilike', "%{$keyword}%");
            });
        }

        $jasas = $query->get(['namaJasa', 'keteranganJasa', 'harga_mulai', 'estimasi_waktu']);

        return [
            'total_layanan' => $jasas->count(),
            'layanan' => $jasas->toArray(),
            'catatan' => 'Harga dapat disesuaikan dengan skala dan kompleksitas fitur kebutuhan klien.',
        ];
    }

    protected function toolSearchPortfolio(?string $queryText): array
    {
        $query = Portfolio::with('type');

        if (! empty($queryText)) {
            $query->where(function ($q) use ($queryText) {
                $q->where('nama_project', 'ilike', "%{$queryText}%")
                    ->orWhere('keterangan_project', 'ilike', "%{$queryText}%")
                    ->orWhereHas('type', function ($t) use ($queryText) {
                        $t->where('nama_jenis', 'ilike', "%{$queryText}%");
                    });
            });
        }

        $portfolios = $query->take(5)->get();

        $data = $portfolios->map(function ($p) {
            return [
                'nama_project' => $p->nama_project,
                'jenis' => $p->type->nama_jenis ?? 'Umum',
                'deskripsi' => $p->keterangan_project,
                'demo_link' => $p->link_project,
            ];
        });

        return [
            'total_ditemukan' => $data->count(),
            'portofolio' => $data->toArray(),
        ];
    }

    protected function toolGetFaqs(): array
    {
        $faqs = Faq::all(['pertanyaan', 'jawaban']);

        return [
            'faqs' => $faqs->toArray(),
        ];
    }

    protected function toolBookConsultation(array $args): array
    {
        $nama = trim($args['nama'] ?? '');
        $telepon = trim($args['nomor_telepon'] ?? '');
        $tanggal = trim($args['tanggal'] ?? '');
        $jam = trim($args['jam'] ?? '');
        $jenisJasa = trim($args['jenis_jasa'] ?? 'Konsultasi Proyek');
        $keterangan = trim($args['keterangan'] ?? 'Permohonan meeting konsultasi via AI Chatbot');

        if (empty($nama) || empty($telepon) || empty($tanggal) || empty($jam)) {
            return [
                'status' => 'error',
                'message' => 'Data belum lengkap. Mohon sertakan nama, nomor telepon, tanggal, dan jam konsultasi.',
            ];
        }

        // Simpan permohonan ke database
        $permohonan = Permohonan::create([
            'namaPermohonan' => $nama,
            'nomorTelepon' => $telepon,
            'jenisJasa' => $jenisJasa,
            'keteranganPermohonan' => $keterangan,
            'tanggal_konsultasi' => $tanggal,
            'jam_konsultasi' => $jam,
            'status' => 'pending',
        ]);

        // Kirim notifikasi interaktif ke Telegram Admin
        try {
            $this->telegramService->sendMeetingBookingNotification($permohonan);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim notif booking ke Telegram: '.$e->getMessage());
        }

        return [
            'status' => 'success',
            'booking_id' => $permohonan->id,
            'klien' => $nama,
            'tanggal' => $tanggal,
            'jam' => $jam,
            'status_jadwal' => 'Menunggu persetujuan tim MDT',
            'message' => 'Jadwal konsultasi berhasil didaftarkan ke sistem dan notifikasi telah dikirimkan ke tim MDT.',
        ];
    }
}
