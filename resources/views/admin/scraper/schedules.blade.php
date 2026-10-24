@extends('layouts.admin')

@section('page_title', 'Manajemen Jadwal Scraping Otomatis')

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

    <!--begin::Form Jadwal Baru-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <i class="ki-duotone ki-calendar-add fs-1 text-primary me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                <h3 class="fw-bold m-0 text-gray-900">Buat Jadwal Scraping Otomatis (Seluruh Indonesia)</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('scraper.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> Kembali ke Scraper
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('scraper.schedules.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">Kategori Usaha / Niche:</label>
                        <select name="keyword_preset" id="sched-keyword-preset" class="form-select" onchange="handleSchedCategoryChange(this)">
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
                        <div id="sched-custom-keyword-wrapper" class="mt-2" style="display: none;">
                            <input type="text" name="keyword_custom" id="sched-input-keyword-custom" class="form-control form-control-sm" placeholder="Contoh: Toko Aki, Bengkel AC...">
                        </div>
                        <input type="hidden" name="keyword" id="sched-final-keyword" value="cafe">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">1. Provinsi:</label>
                        <select name="province" id="sched-province" class="form-select" required>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}" {{ $prov === 'Sumatera Utara' ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">2. Kota / Kabupaten:</label>
                        <select name="city" id="sched-city" class="form-select" required>
                            <!-- Diisi via JS -->
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-gray-700 required">3. Kecamatan:</label>
                        <select name="district" id="sched-district" class="form-select">
                            <!-- Diisi via JS -->
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-check-custom form-check-solid mb-2">
                            <input class="form-check-input" type="checkbox" id="sched-toggle-custom" onchange="toggleSchedCustomLoc(this)">
                            <label class="form-check-label text-gray-700 fw-semibold fs-7" for="sched-toggle-custom">
                                Atau ketik manual wilayah lain? (misal desa/kelurahan/pulau khusus)
                            </label>
                        </div>
                        <div id="sched-custom-location-wrapper" style="display: none;">
                            <input type="text" name="location_custom" id="sched-input-custom-location" class="form-control" placeholder="Contoh: Ubud, Bali atau Menteng, Jakarta Pusat">
                        </div>
                    </div>

                    <!--begin::Filter Dinamis (Radio Buttons)-->
                    <div class="col-12 mt-2">
                        <div class="p-5 rounded-3 bg-light border border-dashed border-gray-300">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-filter-edit fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                <h5 class="fw-bolder text-gray-900 m-0">Kustomisasi Filter Hasil Scraping</h5>
                                <span class="badge badge-light-primary ms-3 fs-9 fw-semibold">Pilih Filter Jadwal</span>
                            </div>

                            <div class="row g-4">
                                <!-- Filter 1: Website -->
                                <div class="col-lg-3 col-sm-6">
                                    <label class="form-label fw-bold text-gray-800 fs-7 mb-2">1. Kepemilikan Website:</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="filter_no_website" value="1" id="sched_filter_web_no" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="sched_filter_web_no">
                                                Hanya Tanpa Website Resmi
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="filter_no_website" value="0" id="sched_filter_web_all">
                                            <label class="form-check-label text-gray-600 fs-8" for="sched_filter_web_all">
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
                                            <input class="form-check-input" type="radio" name="filter_has_phone" value="1" id="sched_filter_phone_yes" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="sched_filter_phone_yes">
                                                Wajib Ada No. Telepon / WA
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="filter_has_phone" value="0" id="sched_filter_phone_all">
                                            <label class="form-check-label text-gray-600 fs-8" for="sched_filter_phone_all">
                                                Semua (Meskipun Tanpa Telepon)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter 3: Minimal Ulasan -->
                                <div class="col-lg-3 col-sm-6">
                                    <label class="form-label fw-bold text-gray-800 fs-7 mb-2">3. Minimal Jumlah Ulasan:</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_reviews" value="5" id="sched_filter_rev_5" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="sched_filter_rev_5">
                                                Minimal > 5 Ulasan (Aktif)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_reviews" value="0" id="sched_filter_rev_0">
                                            <label class="form-check-label text-gray-600 fs-8" for="sched_filter_rev_0">
                                                Semua Ulasan (>= 0)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_reviews" value="20" id="sched_filter_rev_20">
                                            <label class="form-check-label text-gray-600 fs-8" for="sched_filter_rev_20">
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
                                            <input class="form-check-input" type="radio" name="min_rating" value="0" id="sched_filter_rate_all" checked>
                                            <label class="form-check-label text-gray-800 fw-semibold fs-8" for="sched_filter_rate_all">
                                                Semua Rating (Bebas)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_rating" value="4.0" id="sched_filter_rate_4">
                                            <label class="form-check-label text-gray-600 fs-8" for="sched_filter_rate_4">
                                                Rating Bagus (>= 4.0 ★)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-sm">
                                            <input class="form-check-input" type="radio" name="min_rating" value="4.5" id="sched_filter_rate_45">
                                            <label class="form-check-label text-gray-600 fs-8" for="sched_filter_rate_45">
                                                Rating Tinggi (>= 4.5 ★)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Filter Dinamis-->

                    <!-- Baris Bawah: Frekuensi & Tombol Simetris -->
                    <div class="col-12 mt-4 pt-4 border-top">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-gray-800 mb-2">Frekuensi Penjadwalan:</label>
                                <select name="frequency" class="form-select fw-bold" required style="height: 44px;">
                                    <option value="once">Sekali Jalan (Pada waktu tertentu)</option>
                                    <option value="daily" selected>Setiap Hari (Daily Recurring)</option>
                                    <option value="weekly">Setiap Minggu (Weekly Recurring)</option>
                                </select>
                                <span class="text-muted fs-9 mt-1 d-block">Pilih seberapa sering scraping otomatis ini berjalan</span>
                            </div>

                            <div class="col-md-7">
                                <label class="form-label fw-bold text-transparent d-none d-md-block mb-2">Simpan:</label>
                                <button type="submit" class="btn btn-primary w-100 fw-bolder py-3 d-flex align-items-center justify-content-center shadow-sm" style="height: 44px;">
                                    <i class="ki-duotone ki-plus fs-2 me-2"></i> Simpan Jadwal Otomatis
                                </button>
                                <span class="text-muted fs-9 mt-1 d-none d-md-block text-end">Jadwal akan diproses otomatis oleh background scheduler</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mt-5">
                    <i class="ki-duotone ki-time fs-2tx text-info me-4"><span class="path1"></span><span class="path2"></span></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-7 text-gray-800 fw-bold">Penjadwalan Otomatis Laravel Task Scheduler</div>
                            <div class="fs-8 text-gray-600">
                                Jadwal harian/mingguan akan otomatis dipicu secara background oleh perintah <code>php artisan schedule:run</code>. Anda juga dapat menjalankannya langsung melalui <code>php artisan scraper:process-schedules</code>.
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--end::Form Jadwal Baru-->

    <!--begin::Daftar Jadwal Terjadwal-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold m-0 text-gray-900">Daftar Jadwal Aktif</h3>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-50px rounded-start">#ID</th>
                            <th class="min-w-150px">Kategori</th>
                            <th class="min-w-180px">Wilayah / Daerah Target</th>
                            <th class="min-w-120px">Frekuensi</th>
                            <th class="min-w-120px">Status Terakhir</th>
                            <th class="min-w-140px">Terakhir Dijalankan</th>
                            <th class="min-w-100px text-end pe-4 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $sch)
                        <tr>
                            <td class="ps-4 fw-bold text-gray-800">#{{ $sch->id }}</td>
                            <td><span class="text-gray-900 fw-bold">{{ $sch->keyword }}</span></td>
                            <td>
                                <span class="badge badge-light-primary fw-bold">
                                    <i class="ki-duotone ki-geolocation fs-8 me-1"></i>{{ $sch->location }}
                                </span>
                            </td>
                            <td>
                                @if($sch->frequency === 'daily')
                                    <span class="badge badge-light-success fw-bold">Setiap Hari (Daily)</span>
                                @elseif($sch->frequency === 'weekly')
                                    <span class="badge badge-light-info fw-bold">Setiap Minggu (Weekly)</span>
                                @else
                                    <span class="badge badge-light-secondary fw-bold">Sekali Jalan</span>
                                @endif
                            </td>
                            <td>
                                @if($sch->status === 'running')
                                    <span class="badge badge-warning spinner-border-sm">Berjalan...</span>
                                @elseif($sch->status === 'completed')
                                    <span class="badge badge-light-success">Selesai ({{ $sch->leads_count }} leads)</span>
                                @elseif($sch->status === 'failed')
                                    <span class="badge badge-light-danger">Gagal</span>
                                @else
                                    <span class="badge badge-light-secondary">Menunggu Jadwal</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted fs-8">{{ $sch->last_run_at ? $sch->last_run_at->format('d/m/Y H:i') : 'Belum pernah' }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('scraper.tasks.destroy', $sch->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-light-danger btn-sm" title="Hapus Jadwal">
                                        <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-muted">
                                Belum ada jadwal scraping otomatis yang dibuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
    <!--end::Daftar Jadwal Terjadwal-->

</div>

@push('scripts')
<script>
    const schedCategoryPreset = document.getElementById('sched-keyword-preset');
    const schedCustomWrap = document.getElementById('sched-custom-keyword-wrapper');
    const schedCustomInput = document.getElementById('sched-input-keyword-custom');
    const schedFinalKeyword = document.getElementById('sched-final-keyword');

    function handleSchedCategoryChange(select) {
        if (select.value === '__custom__') {
            schedCustomWrap.style.display = 'block';
            schedCustomInput.required = true;
            schedCustomInput.focus();
            schedFinalKeyword.value = schedCustomInput.value.trim() || 'bisnis';
        } else {
            schedCustomWrap.style.display = 'none';
            schedCustomInput.required = false;
            schedFinalKeyword.value = select.value;
        }
    }

    schedCustomInput.addEventListener('input', function() {
        if (schedCategoryPreset.value === '__custom__') {
            schedFinalKeyword.value = this.value.trim() || 'bisnis';
        }
    });

    const schedRegionsData = {!! $regionsJson !!};

    const sProv = document.getElementById('sched-province');
    const sCity = document.getElementById('sched-city');
    const sDist = document.getElementById('sched-district');

    function populateSchedCities(prov) {
        sCity.innerHTML = '';
        if (schedRegionsData[prov]) {
            const cities = Object.keys(schedRegionsData[prov]);
            cities.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                if (c === 'Kota Medan') opt.selected = true;
                sCity.appendChild(opt);
            });
            populateSchedDistricts(prov, sCity.value);
        }
    }

    function populateSchedDistricts(prov, city) {
        sDist.innerHTML = '';
        const defOpt = document.createElement('option');
        defOpt.value = 'Semua Kecamatan';
        defOpt.textContent = '— Semua Kecamatan di ' + city + ' —';
        sDist.appendChild(defOpt);

        if (schedRegionsData[prov] && schedRegionsData[prov][city]) {
            const districts = schedRegionsData[prov][city];
            districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                if (d === 'Medan Baru') opt.selected = true;
                sDist.appendChild(opt);
            });
        }
    }

    sProv.addEventListener('change', function() {
        populateSchedCities(this.value);
    });

    sCity.addEventListener('change', function() {
        populateSchedDistricts(sProv.value, this.value);
    });

    populateSchedCities(sProv.value);

    function toggleSchedCustomLoc(checkbox) {
        const wrap = document.getElementById('sched-custom-location-wrapper');
        const customInput = document.getElementById('sched-input-custom-location');
        if (checkbox.checked) {
            wrap.style.display = 'block';
            customInput.required = true;
            sProv.disabled = true;
            sCity.disabled = true;
            sDist.disabled = true;
        } else {
            wrap.style.display = 'none';
            customInput.required = false;
            customInput.value = '';
            sProv.disabled = false;
            sCity.disabled = false;
            sDist.disabled = false;
        }
    }
</script>
@endpush
@endsection
