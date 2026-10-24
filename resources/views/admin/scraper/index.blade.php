@extends('layouts.admin')

@section('page_title', 'Scraper UMKM Google Maps')

@section('content')
<div class="d-flex flex-column gap-6">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-4">
            <i class="ki-duotone ki-check-circle fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column">
                <h5 class="mb-1 text-success fw-bold">Sukses!</h5>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center p-4">
            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column">
                <h5 class="mb-1 text-danger fw-bold">Perhatian!</h5>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!--begin::Stats Cards-->
    <div class="row g-5 g-xl-8">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0 bg-body">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-shop fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Total Leads UMKM</div>
                            <div class="fs-2x fw-bold text-gray-900">{{ number_format($stats['total_leads']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Data tempat tersimpan di sistem</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0 bg-body">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-warning me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-cloud-cross fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Belum Ada Website</div>
                            <div class="fs-2x fw-bold text-warning">{{ number_format($stats['total_without_website']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Target potensial penawaran website</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0 bg-body">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-success me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-whatsapp fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Kontak WhatsApp Siap</div>
                            <div class="fs-2x fw-bold text-success">{{ number_format($stats['total_with_wa']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Nomor kontak format WA valid</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0 bg-body">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-info me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-time fs-2x text-info"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Total Sesi Scraping</div>
                            <div class="fs-2x fw-bold text-info">{{ number_format($stats['total_tasks']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Riwayat pencarian dijalankan</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats Cards-->

    <!--begin::Live Scraping Progress Banner (Muncul saat proses aktif)-->
    <div id="live-progress-banner" class="card border border-primary border-dashed bg-light-primary shadow-sm" style="display: none;">
        <div class="card-body py-5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div class="d-flex align-items-center">
                    <div class="spinner-border text-primary me-3" role="status" style="width: 2rem; height: 2rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>
                        <h4 class="fw-bolder text-gray-900 mb-0" id="live-banner-title">Sedang Menjalankan Scraping...</h4>
                        <span class="text-muted fs-7" id="live-banner-step">Memulai proses inisialisasi browser...</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="text-end">
                        <div class="fs-8 text-muted">Estimasi Tersisa:</div>
                        <div class="fs-6 fw-bolder text-primary" id="live-banner-eta">Menghitung...</div>
                    </div>
                    <div class="text-end">
                        <div class="fs-8 text-muted">Leads Lolos:</div>
                        <div class="fs-5 fw-bolder text-success" id="live-banner-leads">0</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" id="live-banner-log-btn" onclick="openCurrentLiveLog()">
                        <i class="ki-duotone ki-eye fs-5 me-1"></i> Lihat Log
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="d-flex align-items-center gap-3">
                <div class="progress flex-grow-1 bg-white rounded-pill h-16px shadow-xs">
                    <div id="live-banner-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary rounded-pill fw-bold fs-9" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                <span class="fw-bolder fs-6 text-primary min-w-45px text-end" id="live-banner-percent-text">0%</span>
            </div>
        </div>
    </div>
    <!--end::Live Scraping Progress Banner-->

    <!--begin::Form Scraping Custom-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-geolocation fs-1 text-primary me-3"><span class="path1"></span><span class="path2"></span></i>
                    <h3 class="fw-bold m-0 text-gray-900">Mulai Scraping Baru (Kategori Dinamis & Wilayah Indonesia)</h3>
                </div>
            </div>
            <div class="card-toolbar gap-3">
                <a href="{{ route('scraper.leads') }}" class="btn btn-sm btn-light-success">
                    <i class="ki-duotone ki-element-11 fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> Lihat Data Leads
                </a>
                <a href="{{ route('scraper.schedules') }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-calendar fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> Kelola Jadwal Otomatis
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('scraper.start') }}" method="POST" id="scraper-form">
                @csrf
                <div class="row g-4">
                    <!-- Kategori Dinamis -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">Kategori Usaha / Niche:</label>
                        <select name="keyword_preset" id="select-keyword-preset" class="form-select" onchange="handleCategoryChange(this)">
                            <optgroup label="☕ Kuliner & Minuman">
                                <option value="cafe" selected>Cafe / Kafe</option>
                                <option value="coffee shop">Coffee Shop</option>
                                <option value="restoran">Restoran</option>
                                <option value="rumah makan">Rumah Makan</option>
                                <option value="kuliner">Kuliner / Food Court</option>
                                <option value="bakery & kue">Bakery & Toko Kue</option>
                                <option value="warung kopi">Warung Kopi (Warkop)</option>
                                <option value="katering">Jasa Katering</option>
                            </optgroup>
                            <optgroup label="🚗 Otomotif & Bengkel">
                                <option value="bengkel mobil">Bengkel Mobil</option>
                                <option value="bengkel motor">Bengkel Motor</option>
                                <option value="cuci mobil">Cuci Mobil & Motor (Carwash)</option>
                                <option value="variasi mobil">Variasi & Audio Mobil</option>
                                <option value="rental mobil">Rental Mobil / Motor</option>
                            </optgroup>
                            <optgroup label="✂️ Kecantikan & Fashion">
                                <option value="barbershop">Barbershop / Pangkas Rambut</option>
                                <option value="salon kecantikan">Salon Kecantikan</option>
                                <option value="spa & massage">Spa & Reflexology</option>
                                <option value="butik pakaian">Butik Pakaian & Fashion</option>
                                <option value="konveksi baju">Konveksi & Sablon Baju</option>
                            </optgroup>
                            <optgroup label="🛠️ Layanan Jasa & Retail">
                                <option value="laundry">Laundry Kiloan / Satuan</option>
                                <option value="percetakan">Percetakan & Digital Printing</option>
                                <option value="toko bangunan">Toko Bangunan / Material</option>
                                <option value="petshop">Petshop & Klinik Hewan</option>
                                <option value="toko komputer">Toko Komputer & Servis HP</option>
                                <option value="cuci sepatu">Jasa Cuci Sepatu</option>
                                <option value="studio foto">Studio Foto & Fotografer</option>
                            </optgroup>
                            <optgroup label="🏥 Kesehatan & Edukasi">
                                <option value="klinik gigi">Klinik Gigi</option>
                                <option value="apotek">Apotek</option>
                                <option value="optik">Toko Kacamata / Optik</option>
                                <option value="bimbel">Bimbel / Tempat Kursus</option>
                            </optgroup>
                            <option value="__custom__">✏️ Ketik Kategori Kustom Sendiri...</option>
                        </select>
                        <div id="custom-keyword-wrapper" class="mt-2" style="display: none;">
                            <input type="text" name="keyword_custom" id="input-keyword-custom" class="form-control form-control-sm" placeholder="Contoh: Toko Aki, Bengkel AC, Jahit Jas...">
                        </div>
                        <input type="hidden" name="keyword" id="final-keyword" value="cafe">
                        <span class="text-muted fs-9">Pilih dari preset atau ketik kategori apa saja</span>
                    </div>

                    <!-- Dropdown Wilayah Bertingkat -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">1. Provinsi:</label>
                        <select name="province" id="select-province" class="form-select" required>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}" {{ $prov === 'Sumatera Utara' ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                        <span class="text-muted fs-9">Pilih provinsi</span>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">2. Kota / Kabupaten:</label>
                        <select name="city" id="select-city" class="form-select" required>
                            <!-- Diisi via JS -->
                        </select>
                        <span class="text-muted fs-9">Pilih kota/kabupaten</span>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">3. Kecamatan / Wilayah:</label>
                        <select name="district" id="select-district" class="form-select">
                            <!-- Diisi via JS -->
                        </select>
                        <span class="text-muted fs-9">Pilih kecamatan (atau semua)</span>
                    </div>

                    <div class="col-12 mt-1">
                        <div class="form-check form-check-custom form-check-solid mb-2">
                            <input class="form-check-input" type="checkbox" id="toggle-custom-location" onchange="toggleCustomLoc(this)">
                            <label class="form-check-label text-gray-700 fw-semibold fs-7" for="toggle-custom-location">
                                Ingin ketik manual wilayah tertentu? (misal nama desa, kelurahan, jalan, atau kawasan khusus)
                            </label>
                        </div>
                        <div id="custom-location-wrapper" style="display: none;">
                            <input type="text" name="location_custom" id="input-custom-location" class="form-control" placeholder="Contoh: Babura, Medan Baru atau Kemang, Jakarta Selatan">
                        </div>
                    </div>

                    <!--begin::Filter Dinamis (Radio Buttons)-->
                    <div class="col-12 mt-3">
                        <div class="p-5 rounded-3 bg-light border border-dashed border-gray-300">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-filter-edit fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                <h5 class="fw-bolder text-gray-900 m-0">Kustomisasi Filter Hasil Scraping</h5>
                                <span class="badge badge-light-primary ms-3 fs-9 fw-semibold">Pilih Filter Bebas Sesuai Kebutuhan</span>
                            </div>

                            <div class="row g-4">
                                <!-- Filter 1: Website -->
                                <div class="col-lg-3 col-sm-6">
                                    <label class="form-label fw-bold text-gray-800 fs-7 mb-2">1. Kepemilikan Website:</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="filter_no_website" value="1" id="filter_web_no" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="filter_web_no">
                                                Hanya Tanpa Website Resmi
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="filter_no_website" value="0" id="filter_web_all">
                                            <label class="form-check-label text-gray-600 fs-8" for="filter_web_all">
                                                Semua (Abaikan Cek Website)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter 2: Telepon / WhatsApp -->
                                <div class="col-lg-3 col-sm-6">
                                    <label class="form-label fw-bold text-gray-800 fs-7 mb-2">2. Kontak Telepon / WA:</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="filter_has_phone" value="1" id="filter_phone_yes" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="filter_phone_yes">
                                                Wajib Ada No. Telepon / WA
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="filter_has_phone" value="0" id="filter_phone_all">
                                            <label class="form-check-label text-gray-600 fs-8" for="filter_phone_all">
                                                Semua (Termasuk Tanpa Telepon)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter 3: Minimal Ulasan -->
                                <div class="col-lg-3 col-sm-6">
                                    <label class="form-label fw-bold text-gray-800 fs-7 mb-2">3. Minimal Jumlah Ulasan:</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_reviews" value="5" id="filter_rev_5" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="filter_rev_5">
                                                Minimal > 5 Ulasan (Aktif)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_reviews" value="0" id="filter_rev_0">
                                            <label class="form-check-label text-gray-600 fs-8" for="filter_rev_0">
                                                Semua Ulasan (>= 0)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_reviews" value="20" id="filter_rev_20">
                                            <label class="form-check-label text-gray-600 fs-8" for="filter_rev_20">
                                                Populer (> 20 Ulasan)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter 4: Rating Bintang -->
                                <div class="col-lg-3 col-sm-6">
                                    <label class="form-label fw-bold text-gray-800 fs-7 mb-2">4. Minimal Rating Bintang:</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_rating" value="0" id="filter_rate_all" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="filter_rate_all">
                                                Semua Rating (Bebas)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_rating" value="4.0" id="filter_rate_4">
                                            <label class="form-check-label text-gray-600 fs-8" for="filter_rate_4">
                                                Rating Bagus (>= 4.0 ★)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_rating" value="4.5" id="filter_rate_45">
                                            <label class="form-check-label text-gray-600 fs-8" for="filter_rate_45">
                                                Rating Tinggi (>= 4.5 ★)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Filter Dinamis-->

                    <!-- Baris Bawah: Limit Leads & Tombol Simetris -->
                    <div class="col-12 mt-4 pt-4 border-top">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-gray-800 mb-2">Target Jumlah Leads:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="ki-duotone ki-people fs-4 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>
                                    <input type="number" name="limit" class="form-control fw-bold" value="20" min="1" max="100" required style="height: 44px;">
                                    <span class="input-group-text bg-light text-muted fs-8">UMKM</span>
                                </div>
                                <span class="text-muted fs-9 mt-1 d-block">Jumlah tempat yang akan disimpan setelah lolos filter</span>
                            </div>

                            <div class="col-md-7">
                                <label class="form-label fw-bold text-transparent d-none d-md-block mb-2">Eksekusi:</label>
                                <button type="submit" class="btn btn-primary w-100 fw-bolder py-3 d-flex align-items-center justify-content-center shadow-sm" id="btn-submit-scraper" style="height: 44px;">
                                    <i class="ki-duotone ki-send fs-2 me-2"><span class="path1"></span><span class="path2"></span></i> Mulai Scraping Sekarang
                                </button>
                                <span class="text-muted fs-9 mt-1 d-none d-md-block text-end">Jeda otomatis 5–7 detik per tempat untuk proteksi anti-blokir</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4 mt-5">
                    <i class="ki-duotone ki-shield-tick fs-2tx text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-7 text-gray-800 fw-bold">Live Progress & Proteksi Anti-Blokir (Jeda 5–7 Detik)</div>
                            <div class="fs-8 text-gray-600">
                                Proses scraping dilengkapi <strong>Live Progress Bar</strong> dan <strong>Estimasi Waktu Selesai (ETA)</strong>. Scraper memeriksa setiap tempat dengan jeda aman 5-7 detik dan hanya menyimpan tempat <strong>tanpa website resmi</strong>, <strong>ada nomor telepon/WA</strong>, dan <strong>ulasan > 5</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--end::Form Scraping Custom-->

    <!--begin::Tabel Riwayat Tugas-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold m-0 text-gray-900">Riwayat Tugas Scraping & Keterangan Hasil</h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-light" onclick="location.reload();" title="Refresh">
                    <i class="ki-duotone ki-arrows-circle fs-3"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-50px rounded-start">#ID</th>
                            <th class="min-w-160px">Kategori & Target</th>
                            <th class="min-w-100px">Frekuensi</th>
                            <th class="min-w-200px">Status & Progres Selesai</th>
                            <th class="min-w-80px text-center">Leads Didapat</th>
                            <th class="min-w-200px">Keterangan / Log Terakhir</th>
                            <th class="min-w-120px">Waktu</th>
                            <th class="min-w-120px text-end pe-4 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr id="task-row-{{ $task->id }}">
                            <td class="ps-4 fw-bold text-gray-800">#{{ $task->id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-900 fw-bold fs-6">{{ $task->keyword }}</span>
                                    <span class="text-muted fs-7"><i class="ki-duotone ki-geolocation fs-7 text-primary me-1"></i>{{ $task->location }}</span>
                                </div>
                            </td>
                            <td>
                                @if($task->frequency === 'once')
                                    <span class="badge badge-light-secondary">Sekali Jalan</span>
                                @elseif($task->frequency === 'daily')
                                    <span class="badge badge-light-primary">Harian</span>
                                @elseif($task->frequency === 'weekly')
                                    <span class="badge badge-light-info">Mingguan</span>
                                @endif
                            </td>
                            <td id="task-status-cell-{{ $task->id }}">
                                @if($task->status === 'running')
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="badge badge-warning spinner-border-sm py-1 px-2 fw-bold">
                                                <span class="spinner-grow spinner-grow-sm me-1" role="status"></span> Sedang Jalan...
                                            </span>
                                            <span class="fs-8 fw-bold text-primary" id="task-percent-text-{{ $task->id }}">{{ $task->progress_percent ?? 0 }}%</span>
                                        </div>
                                        <div class="progress h-8px w-100 bg-light-primary rounded-pill">
                                            <div id="task-progressbar-{{ $task->id }}" class="progress-bar bg-primary progress-bar-striped progress-bar-animated rounded-pill" style="width: {{ $task->progress_percent ?? 5 }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted fs-9">
                                            <span id="task-step-{{ $task->id }}">{{ Str::limit($task->current_step ?? 'Memproses...', 30) }}</span>
                                            <span id="task-eta-{{ $task->id }}" class="text-primary fw-semibold">
                                                @if($task->eta_seconds && $task->eta_seconds > 0)
                                                    ± {{ $task->eta_seconds }}s
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @elseif($task->status === 'completed')
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge badge-light-success fw-bold py-2 px-3">
                                            <i class="ki-duotone ki-check-circle fs-6 text-success me-1"><span class="path1"></span><span class="path2"></span></i> Sukses Selesai (100%)
                                        </span>
                                    </div>
                                @elseif($task->status === 'failed')
                                    <span class="badge badge-light-danger fw-bold py-2 px-3">
                                        <i class="ki-duotone ki-cross-circle fs-6 text-danger me-1"><span class="path1"></span><span class="path2"></span></i> Gagal Eksekusi
                                    </span>
                                @else
                                    <span class="badge badge-light-secondary fw-bold py-2 px-3">Menunggu Antrean</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-primary fs-7 fw-bolder px-3 py-2" id="leads-count-{{ $task->id }}">
                                    {{ $task->leads_count }}
                                </span>
                            </td>
                            <td>
                                <div class="text-muted fs-8 text-truncate" style="max-width: 250px;" title="{{ $task->log_message }}" id="task-log-{{ $task->id }}">
                                    {{ Str::limit($task->log_message ?? 'Belum ada log', 70) }}
                                </div>
                            </td>
                            <td>
                                <span class="text-muted fs-8">{{ $task->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-icon btn-light-info btn-sm" onclick="showTaskLogModal({{ $task->id }})" title="Lihat Detail & Log">
                                        <i class="ki-duotone ki-information fs-4"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                    <a href="{{ route('scraper.leads', ['location' => $task->location]) }}" class="btn btn-icon btn-light-primary btn-sm" title="Lihat Leads">
                                        <i class="ki-duotone ki-eye fs-4"><span class="path1"></span><span class="path2"></span></i>
                                    </a>
                                    <form action="{{ route('scraper.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-light-danger btn-sm" title="Hapus Tugas">
                                            <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-muted">
                                <i class="ki-duotone ki-magnifier fs-3x text-muted mb-2"><span class="path1"></span><span class="path2"></span></i>
                                <div>Belum ada riwayat tugas scraping. Pilih kategori & wilayah di atas untuk memulai!</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
    <!--end::Tabel Riwayat Tugas-->

</div>

<!--begin::Modal Detail Keterangan & Log-->
<div class="modal fade" id="modal-task-log" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-700px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="fw-bold" id="modal-log-title">Detail Keterangan & Progres Tugas</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-6 px-lg-8">
                <div class="d-flex align-items-center mb-4 p-4 rounded bg-light" id="modal-status-badge-container">
                    <div id="modal-status-badge"></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="fw-bold text-gray-900" id="modal-target-info"></div>
                        <div class="text-muted fs-8" id="modal-time-info"></div>
                    </div>
                </div>

                <!-- Modal Progress Bar -->
                <div class="mb-4 p-4 rounded border bg-light-primary" id="modal-progress-wrapper">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold fs-7 text-gray-800" id="modal-step-text">Status Pengerjaan</span>
                        <span class="fw-bolder fs-7 text-primary" id="modal-percent-text">0%</span>
                    </div>
                    <div class="progress h-12px bg-white rounded-pill mb-2">
                        <div id="modal-progressbar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary rounded-pill" style="width: 0%"></div>
                    </div>
                    <div class="d-flex justify-content-between fs-8 text-muted">
                        <span>Estimasi Waktu: <strong class="text-gray-800" id="modal-eta-text">-</strong></span>
                        <span>Leads Ditemukan: <strong class="text-success" id="modal-leads-count">0</strong></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-gray-800">Catatan Log Proses:</label>
                    <div class="p-4 bg-dark text-white rounded font-monospace fs-8" style="max-height: 300px; overflow-y: auto; white-space: pre-wrap;" id="modal-log-content">
                        Memuat data log...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Detail Keterangan & Log-->

@push('scripts')
<script>
    // 1. Kategori Dinamis
    const keywordPreset = document.getElementById('select-keyword-preset');
    const customKeywordWrap = document.getElementById('custom-keyword-wrapper');
    const customKeywordInput = document.getElementById('input-keyword-custom');
    const finalKeyword = document.getElementById('final-keyword');

    function handleCategoryChange(select) {
        if (select.value === '__custom__') {
            customKeywordWrap.style.display = 'block';
            customKeywordInput.required = true;
            customKeywordInput.focus();
            finalKeyword.value = customKeywordInput.value.trim() || 'bisnis';
        } else {
            customKeywordWrap.style.display = 'none';
            customKeywordInput.required = false;
            finalKeyword.value = select.value;
        }
    }

    customKeywordInput.addEventListener('input', function() {
        if (keywordPreset.value === '__custom__') {
            finalKeyword.value = this.value.trim() || 'bisnis';
        }
    });

    // 2. Dropdown Wilayah Indonesia
    const regionsData = {!! $regionsJson !!};

    const provSelect = document.getElementById('select-province');
    const citySelect = document.getElementById('select-city');
    const distSelect = document.getElementById('select-district');

    function populateCities(province) {
        citySelect.innerHTML = '';
        if (regionsData[province]) {
            const cities = Object.keys(regionsData[province]);
            cities.forEach(city => {
                const opt = document.createElement('option');
                opt.value = city;
                opt.textContent = city;
                if (city === 'Kota Medan') opt.selected = true;
                citySelect.appendChild(opt);
            });
            populateDistricts(province, citySelect.value);
        }
    }

    function populateDistricts(province, city) {
        distSelect.innerHTML = '';
        const defaultOpt = document.createElement('option');
        defaultOpt.value = 'Semua Kecamatan';
        defaultOpt.textContent = '— Semua Kecamatan di ' + city + ' —';
        distSelect.appendChild(defaultOpt);

        if (regionsData[province] && regionsData[province][city]) {
            const districts = regionsData[province][city];
            districts.forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist;
                opt.textContent = dist;
                if (dist === 'Medan Selayang') opt.selected = true;
                distSelect.appendChild(opt);
            });
        }
    }

    provSelect.addEventListener('change', function() {
        populateCities(this.value);
    });

    citySelect.addEventListener('change', function() {
        populateDistricts(provSelect.value, this.value);
    });

    populateCities(provSelect.value);

    function toggleCustomLoc(checkbox) {
        const wrap = document.getElementById('custom-location-wrapper');
        const customInput = document.getElementById('input-custom-location');
        if (checkbox.checked) {
            wrap.style.display = 'block';
            customInput.required = true;
            provSelect.disabled = true;
            citySelect.disabled = true;
            distSelect.disabled = true;
        } else {
            wrap.style.display = 'none';
            customInput.required = false;
            customInput.value = '';
            provSelect.disabled = false;
            citySelect.disabled = false;
            distSelect.disabled = false;
        }
    }

    // 3. Modal Task Log
    let currentModalTaskId = null;
    async function showTaskLogModal(id) {
        currentModalTaskId = id;
        const modal = new bootstrap.Modal(document.getElementById('modal-task-log'));
        document.getElementById('modal-log-content').innerText = 'Mengambil log terbaru...';
        modal.show();
        await updateModalContent(id);
    }

    function openCurrentLiveLog() {
        if (activeRunningTaskId) {
            showTaskLogModal(activeRunningTaskId);
        }
    }

    async function updateModalContent(id) {
        try {
            const res = await fetch(`{{ url('/scraper/tasks') }}/${id}/status`);
            if (res.ok) {
                const data = await res.json();
                document.getElementById('modal-target-info').innerText = `${data.keyword} di ${data.location}`;
                document.getElementById('modal-time-info').innerText = `Dibuat: ${data.created_at || '-'} | Selesai / Terakhir: ${data.last_run_at || '-'}`;

                let badgeHtml = '';
                if (data.status === 'completed') {
                    badgeHtml = '<span class="badge badge-success py-2 px-3 fw-bold">SUKSES SELESAI</span>';
                } else if (data.status === 'failed') {
                    badgeHtml = '<span class="badge badge-danger py-2 px-3 fw-bold">GAGAL EKSEKUSI</span>';
                } else if (data.status === 'running') {
                    badgeHtml = '<span class="badge badge-warning py-2 px-3 fw-bold">SEDANG BERJALAN</span>';
                } else {
                    badgeHtml = '<span class="badge badge-secondary py-2 px-3 fw-bold">MENUNGGU</span>';
                }
                document.getElementById('modal-status-badge').innerHTML = badgeHtml;

                // Progress di modal
                const percent = data.progress_percent || 0;
                document.getElementById('modal-percent-text').innerText = percent + '%';
                document.getElementById('modal-progressbar').style.width = percent + '%';
                document.getElementById('modal-step-text').innerText = data.current_step || 'Memproses...';
                document.getElementById('modal-eta-text').innerText = data.eta_text || '-';
                document.getElementById('modal-leads-count').innerText = data.leads_count || 0;

                document.getElementById('modal-log-content').innerText = data.log_message || 'Belum ada log tercatat.';
            }
        } catch (e) {
            document.getElementById('modal-log-content').innerText = 'Gagal memuat log: ' + e.message;
        }
    }

    // 4. Live Polling & Progress Engine
    const runningTasks = [
        @foreach($tasks->whereIn('status', ['running', 'pending']) as $rt)
            {{ $rt->id }},
        @endforeach
    ];

    let activeRunningTaskId = runningTasks.length > 0 ? runningTasks[0] : null;

    if (runningTasks.length > 0) {
        const liveBanner = document.getElementById('live-progress-banner');
        liveBanner.style.display = 'block';

        const interval = setInterval(async () => {
            let stillRunning = false;
            for (const id of runningTasks) {
                try {
                    const res = await fetch(`{{ url('/scraper/tasks') }}/${id}/status`);
                    if (res.ok) {
                        const data = await res.json();
                        
                        // Update Banner
                        if (id === activeRunningTaskId) {
                            document.getElementById('live-banner-title').innerText = `Sedang Scraping: ${data.keyword} di ${data.location}`;
                            document.getElementById('live-banner-step').innerText = data.current_step || 'Sedang memproses...';
                            document.getElementById('live-banner-eta').innerText = data.eta_text || 'Menghitung...';
                            document.getElementById('live-banner-leads').innerText = data.leads_count || 0;
                            
                            const percent = data.progress_percent || 5;
                            document.getElementById('live-banner-progress-bar').style.width = percent + '%';
                            document.getElementById('live-banner-progress-bar').innerText = percent + '%';
                            document.getElementById('live-banner-percent-text').innerText = percent + '%';
                        }

                        // Update baris tabel
                        const countEl = document.getElementById(`leads-count-${id}`);
                        const logEl = document.getElementById(`task-log-${id}`);
                        const percentEl = document.getElementById(`task-percent-text-${id}`);
                        const pBarEl = document.getElementById(`task-progressbar-${id}`);
                        const stepEl = document.getElementById(`task-step-${id}`);
                        const etaEl = document.getElementById(`task-eta-${id}`);

                        if (countEl) countEl.innerText = data.leads_count;
                        if (logEl) logEl.innerText = data.log_message ? data.log_message.substring(0, 70) + '...' : '';
                        if (percentEl) percentEl.innerText = (data.progress_percent || 0) + '%';
                        if (pBarEl) pBarEl.style.width = (data.progress_percent || 5) + '%';
                        if (stepEl) stepEl.innerText = (data.current_step || 'Memproses...').substring(0, 30);
                        if (etaEl) etaEl.innerText = data.eta_text || '-';

                        // Update modal jika sedang terbuka
                        if (currentModalTaskId === id) {
                            updateModalContent(id);
                        }

                        if (data.status === 'running' || data.status === 'pending') {
                            stillRunning = true;
                        }
                    }
                } catch (e) {
                    console.error(e);
                }
            }

            if (!stillRunning) {
                clearInterval(interval);
                liveBanner.style.display = 'none';
                setTimeout(() => location.reload(), 1500);
            }
        }, 3000); // Polling tiap 3 detik untuk animasi halus
    }
</script>
@endpush
@endsection
