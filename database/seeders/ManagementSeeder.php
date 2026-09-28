<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Jasa;
use App\Models\Keunggulan;
use App\Models\Permohonan;
use App\Models\Berita;
use App\Models\CompanyProfile;
use App\Models\About;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\Partner;
use App\Models\ProjectType;
use App\Models\Portfolio;

class ManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seeder untuk Company Profile
        CompanyProfile::create([
            'nama_profil' => 'MDT Kreatif Digital',
            'instagram' => 'https://instagram.com/mdt_kreatif',
            'tiktok' => 'https://tiktok.com/@mdt_kreatif',
            'thread' => 'https://threads.net/@mdt_kreatif',
            'email' => 'halo@mdtkreatif.com',
            'nomor_telepon' => '08111222333',
            'alamat' => 'Jl. Teknologi Canggih No. 123, Jakarta Selatan',
        ]);

        // 2. Seeder untuk About
        About::create([
            'keterangan_tentang' => 'Kami adalah agensi digital inovatif yang berfokus pada transformasi digital. Berdiri sejak tahun 2020, kami telah membantu puluhan perusahaan dari skala UMKM hingga korporasi besar dalam membangun kehadiran digital yang kuat dan profesional.',
        ]);

        // 3. Seeder untuk FAQ
        $faqs = [
            [
                'pertanyaan' => 'Berapa lama waktu pengerjaan sebuah website?',
                'jawaban' => 'Waktu pengerjaan sangat bergantung pada kompleksitas fitur. Untuk website company profile standar membutuhkan waktu 1-2 minggu, sementara untuk aplikasi kompleks bisa memakan waktu 1-3 bulan.',
            ],
            [
                'pertanyaan' => 'Apakah disediakan layanan perbaikan (maintenance)?',
                'jawaban' => 'Tentu. Setiap pembuatan sistem melalui kami sudah termasuk garansi maintenance gratis selama 3 bulan pertama.',
            ],
            [
                'pertanyaan' => 'Bagaimana sistem pembayarannya?',
                'jawaban' => 'Pembayaran dibagi menjadi 3 tahap: DP 30% di awal, 40% setelah desain disetujui, dan 30% setelah aplikasi siap live.',
            ]
        ];
        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

        // 4. Seeder untuk Testimonial
        $testimonials = [
            [
                'nama_testimoni' => 'Ahmad Reza - CEO PT. Sinar Abadi',
                'keterangan_testimoni' => 'Aplikasi kasir yang dibuatkan sangat membantu kami. Laporannya akurat dan mudah digunakan.',
            ],
            [
                'nama_testimoni' => 'Siti Aminah - Owner Butik Cantik',
                'keterangan_testimoni' => 'Desain websitenya sangat cantik dan elegan. Omset penjualan online saya naik drastis!',
            ]
        ];
        foreach ($testimonials as $testi) {
            Testimonial::create($testi);
        }

        // 5. Seeder untuk Partner
        $partners = [
            ['nama_perusahaan' => 'Google Cloud Partner'],
            ['nama_perusahaan' => 'AWS Partner Network'],
            ['nama_perusahaan' => 'Midtrans Payment'],
            ['nama_perusahaan' => 'Telkom Indonesia']
        ];
        foreach ($partners as $partner) {
            Partner::create($partner);
        }

        // 6. Seeder untuk Project Type
        $projectTypesData = [
            'Company Profile',
            'E-Commerce / Toko Online',
            'Sistem Informasi (ERP/HRIS)',
            'Mobile App (Android/iOS)'
        ];
        
        $projectTypeUuids = [];
        foreach ($projectTypesData as $type) {
            $pt = ProjectType::create(['nama_jenis' => $type]);
            $projectTypeUuids[$type] = $pt->uuid;
        }

        // 7. Seeder untuk Portfolio
        $portfolios = [
            [
                'nama_project' => 'E-Commerce Skincare',
                'jenis_project' => $projectTypeUuids['E-Commerce / Toko Online'] ?? null,
                'keterangan_project' => 'Platform toko online skincare dengan integrasi pembayaran otomatis dan manajemen stok multikota.',
                'link_project' => 'https://demo-skincare.com',
            ],
            [
                'nama_project' => 'Aplikasi Presensi Karyawan',
                'jenis_project' => $projectTypeUuids['Sistem Informasi (ERP/HRIS)'] ?? null,
                'keterangan_project' => 'Sistem HRIS lengkap dengan fitur absensi face recognition berbasis GPS.',
                'link_project' => null,
            ]
        ];
        foreach ($portfolios as $portfolio) {
            Portfolio::create($portfolio);
        }

        // 8. Seeder untuk Jasa
        $jasas = [
            [
                'namaJasa' => 'Pengembangan Aplikasi Web',
                'keteranganJasa' => 'Layanan pembuatan aplikasi berbasis web yang responsif dan scalable menggunakan teknologi modern.',
            ],
            [
                'namaJasa' => 'Pembuatan Aplikasi Mobile',
                'keteranganJasa' => 'Membangun aplikasi mobile untuk iOS dan Android dengan antarmuka yang user-friendly.',
            ],
            [
                'namaJasa' => 'Konsultasi IT & Sistem',
                'keteranganJasa' => 'Layanan konsultasi untuk memecahkan masalah IT dan meningkatkan efisiensi sistem perusahaan.',
            ],
            [
                'namaJasa' => 'Optimasi UI/UX Desain',
                'keteranganJasa' => 'Perancangan desain antarmuka dan pengalaman pengguna yang menarik dan intuitif.',
            ]
        ];
        foreach ($jasas as $jasa) {
            Jasa::create($jasa);
        }

        // 9. Seeder untuk Keunggulan
        $keunggulans = [
            [
                'namaUnggulan' => 'Tim Profesional',
                'keteranganUnggulan' => 'Kami memiliki tim ahli yang bersertifikasi dan berpengalaman di bidangnya masing-masing.',
            ],
            [
                'namaUnggulan' => 'Dukungan 24/7',
                'keteranganUnggulan' => 'Layanan bantuan pelanggan yang siap merespon masalah dan kebutuhan Anda kapan saja.',
            ],
            [
                'namaUnggulan' => 'Harga Terjangkau',
                'keteranganUnggulan' => 'Kami menawarkan harga yang kompetitif tanpa mengurangi kualitas pelayanan.',
            ],
            [
                'namaUnggulan' => 'Pengerjaan Cepat',
                'keteranganUnggulan' => 'Proyek diselesaikan dengan tepat waktu sesuai dengan target yang telah disepakati.',
            ]
        ];
        foreach ($keunggulans as $keunggulan) {
            Keunggulan::create($keunggulan);
        }

        // 10. Seeder untuk Permohonan
        $permohonans = [
            [
                'namaPermohonan' => 'PT. Maju Mundur',
                'nomorTelepon' => '081234567890',
                'jenisJasa' => 'Pengembangan Aplikasi Web',
                'keteranganPermohonan' => 'Ingin membuat sistem informasi HRD untuk 500 karyawan.',
            ],
            [
                'namaPermohonan' => 'CV. Jaya Selalu',
                'nomorTelepon' => '085712345678',
                'jenisJasa' => 'Pembuatan Aplikasi Mobile',
                'keteranganPermohonan' => 'Membutuhkan aplikasi e-commerce sederhana.',
            ],
            [
                'namaPermohonan' => 'Budi Santoso',
                'nomorTelepon' => '089876543210',
                'jenisJasa' => 'Optimasi UI/UX Desain',
                'keteranganPermohonan' => 'Ingin meredesain website perusahaan agar lebih modern.',
            ]
        ];
        foreach ($permohonans as $permohonan) {
            Permohonan::create($permohonan);
        }

        // 11. Seeder untuk Berita
        $beritas = [
            [
                'namaBerita' => 'Peluncuran Aplikasi Terbaru',
                'slugBerita' => Str::slug('Peluncuran Aplikasi Terbaru'),
                'author' => 'Admin Utama',
                'keteranganBerita' => 'Kami sangat antusias mengumumkan peluncuran aplikasi terbaru kami yang akan merevolusi cara Anda bekerja. Aplikasi ini dilengkapi dengan fitur AI terbaru yang dapat mengotomatisasi tugas-tugas repetitif Anda.',
                'gambar' => null
            ],
            [
                'namaBerita' => 'Tips Memilih Jasa Pembuatan Website',
                'slugBerita' => Str::slug('Tips Memilih Jasa Pembuatan Website'),
                'author' => 'Tim Marketing',
                'keteranganBerita' => 'Memilih jasa pembuatan website yang tepat sangat krusial bagi bisnis Anda. Pastikan Anda memperhatikan portofolio, testimoni klien, dan layanan purna jual yang mereka tawarkan.',
                'gambar' => null
            ],
            [
                'namaBerita' => 'Pentingnya UI/UX Dalam Aplikasi',
                'slugBerita' => Str::slug('Pentingnya UI/UX Dalam Aplikasi'),
                'author' => 'Tim Desain',
                'keteranganBerita' => 'Desain UI/UX bukan sekadar membuat aplikasi terlihat bagus, melainkan tentang bagaimana pengguna dapat mencapai tujuan mereka dengan mudah saat menggunakan aplikasi Anda.',
                'gambar' => null
            ]
        ];
        foreach ($beritas as $berita) {
            Berita::create($berita);
        }
    }
}
