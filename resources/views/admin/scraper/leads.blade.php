@extends('layouts.admin')

@section('page_title', 'Data Leads UMKM Google Maps')

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

    <!--begin::Main Card-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title flex-wrap gap-3">
                <form id="filter-form" action="{{ route('scraper.leads') }}" method="GET" class="d-flex align-items-center flex-wrap gap-3">
                    <div class="position-relative">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500 position-absolute top-50 translate-middle-y ms-4"></i>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid ps-12" placeholder="Cari nama cafe / no. WA..." style="width: 220px;">
                    </div>
                    <select name="location" class="form-select form-select-solid" style="width: 170px;" onchange="this.form.submit()">
                        <option value="">Semua Wilayah</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-select form-select-solid" style="width: 140px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Baru (New)</option>
                        <option value="queued_blast" {{ request('status') == 'queued_blast' ? 'selected' : '' }}>Di Blast</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Dihubungi</option>
                    </select>
                    <select name="per_page" class="form-select form-select-solid" style="width: 120px;" onchange="this.form.submit()" title="Jumlah data per halaman">
                        <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10 / hal</option>
                        <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20 / hal</option>
                        <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50 / hal</option>
                        <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100 / hal</option>
                    </select>
                    <button type="submit" class="btn btn-icon btn-light" title="Terapkan Filter">
                        <i class="ki-duotone ki-filter fs-4"></i>
                    </button>
                    @if(request()->anyFilled(['q', 'location', 'status', 'per_page']))
                        <a href="{{ route('scraper.leads') }}" class="btn btn-sm btn-link text-muted">Reset</a>
                    @endif
                </form>
            </div>
            <div class="card-toolbar gap-2 flex-wrap">
                <a href="{{ route('scraper.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-plus fs-4 me-1"></i> Scrap Baru
                </a>
                <button type="button" class="btn btn-sm btn-danger fw-bold d-none" id="btn-top-bulk-delete" onclick="confirmBulkDelete()">
                    <i class="ki-duotone ki-trash fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    Hapus Terpilih (<span class="selected-count-label">0</span>)
                </button>
                <button type="button" class="btn btn-sm btn-success fw-bold" onclick="openPushBlastModal()">
                    <i class="ki-duotone ki-whatsapp fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                    Kirim ke WA Blast <span id="top-blast-count" class="badge badge-light-success ms-1 d-none">0</span>
                </button>
            </div>
        </div>

        <div class="card-body pt-2">
            <!--begin::Bulk Selection Floating Bar-->
            <div id="bulk-action-bar" class="alert alert-primary d-flex justify-content-between align-items-center p-3 mb-4 rounded-3 d-none shadow-sm">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <i class="ki-duotone ki-check-circle fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    <span class="fw-bold text-gray-900 fs-7">
                        <strong id="selected-badge-count" class="text-primary fs-6">0</strong> data lead UMKM dipilih
                    </span>
                    <button type="button" class="btn btn-link btn-color-muted btn-active-color-primary btn-sm ms-2 p-0 text-decoration-underline" onclick="deselectAll()">
                        Batal Pilih Semua
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-danger fw-semibold" onclick="confirmBulkDelete()">
                        <i class="ki-duotone ki-trash fs-5 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        Hapus Terpilih (<span class="selected-count-label">0</span>)
                    </button>
                    <button type="button" class="btn btn-sm btn-success fw-semibold" onclick="openPushBlastModal()">
                        <i class="ki-duotone ki-whatsapp fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                        Kirim ke WA Blast (<span class="selected-count-label">0</span>)
                    </button>
                </div>
            </div>
            <!--end::Bulk Selection Floating Bar-->

            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 w-40px rounded-start">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="check-all" onclick="toggleCheckAll(this)" title="Pilih Semua di Halaman Ini">
                                </div>
                            </th>
                            <th class="min-w-200px">Nama Bisnis & Maps</th>
                            <th class="min-w-120px">Kategori & Wilayah</th>
                            <th class="min-w-150px">Kontak WhatsApp</th>
                            <th class="min-w-120px text-center">Rating & Review</th>
                            <th class="min-w-140px">Status Website</th>
                            <th class="min-w-100px">Status Lead</th>
                            <th class="min-w-100px text-end pe-4 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        <tr id="row-lead-{{ $lead->id }}">
                            <td class="ps-4">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input lead-checkbox" type="checkbox" value="{{ $lead->id }}" data-has-wa="{{ !empty($lead->phone_wa) ? '1' : '0' }}" onchange="updateSelectionUI()">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="{{ $lead->maps_url ?? '#' }}" target="_blank" class="text-gray-900 fw-bold text-hover-primary fs-6 d-flex align-items-center">
                                        {{ $lead->name }}
                                        <i class="ki-duotone ki-exit-right-corner fs-7 text-muted ms-1"></i>
                                    </a>
                                    @if($lead->address)
                                        <span class="text-muted fs-8 text-truncate" style="max-width: 280px;" title="{{ $lead->address }}">
                                            <i class="ki-duotone ki-geolocation fs-8 text-danger me-1"></i>{{ $lead->address }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-primary fw-bold mb-1">{{ $lead->category ?? 'Kuliner' }}</span>
                                <div class="text-muted fs-8">{{ $lead->location_tag ?? '-' }}</div>
                            </td>
                            <td>
                                @if($lead->phone_wa)
                                    <div class="d-flex align-items-center">
                                        <a href="https://wa.me/{{ $lead->phone_wa }}" target="_blank" class="badge badge-light-success fw-bolder py-2 px-3 text-hover-success fs-7">
                                            <i class="ki-duotone ki-whatsapp fs-6 text-success me-1"><span class="path1"></span><span class="path2"></span></i>
                                            {{ $lead->phone_wa }}
                                        </a>
                                    </div>
                                @else
                                    <span class="text-muted fs-8">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge badge-light-warning fw-bold fs-7">
                                        ★ {{ $lead->rating ? number_format($lead->rating, 1) : '-' }}
                                    </span>
                                    <span class="text-muted fs-8 mt-1">({{ number_format($lead->review_count) }} ulasan)</span>
                                </div>
                            </td>
                            <td>
                                @if($lead->website_url)
                                    <span class="badge badge-light-info fw-bold" title="{{ $lead->website_url }}">
                                        Hanya Sosmed
                                    </span>
                                    <a href="{{ $lead->website_url }}" target="_blank" class="text-muted fs-9 d-block text-truncate" style="max-width: 140px;">
                                        {{ $lead->website_url }}
                                    </a>
                                @else
                                    <span class="badge badge-light-danger fw-bold">
                                        Tanpa Website
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($lead->status === 'new')
                                    <span class="badge badge-light-primary fw-bold">Baru (New)</span>
                                @elseif($lead->status === 'queued_blast')
                                    <span class="badge badge-light-warning fw-bold">Masuk Blast</span>
                                @elseif($lead->status === 'contacted')
                                    <span class="badge badge-light-success fw-bold">Dihubungi</span>
                                @else
                                    <span class="badge badge-light-secondary">{{ $lead->status }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($lead->phone_wa)
                                        <a href="https://wa.me/{{ $lead->phone_wa }}?text=Halo%20{{ urlencode($lead->name) }}%2C%20kami%20dari%20MDT%20Developer..." target="_blank" class="btn btn-icon btn-light-success btn-sm" title="Chat WhatsApp Langsung">
                                            <i class="ki-duotone ki-whatsapp fs-4"><span class="path1"></span><span class="path2"></span></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deleteLead({{ $lead->id }}, '{{ addslashes($lead->name) }}')" title="Hapus Lead">
                                        <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-muted">
                                <i class="ki-duotone ki-geolocation fs-3x text-muted mb-2"><span class="path1"></span><span class="path2"></span></i>
                                <div>Belum ada data leads UMKM yang cocok. Jalankan scraper untuk mengumpulkan data baru!</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!--begin::Pagination Area-->
            <div class="d-flex justify-content-between align-items-center mt-6 flex-wrap gap-3">
                <div class="text-muted fs-7">
                    Menampilkan <span class="fw-bold text-gray-800">{{ $leads->firstItem() ?? 0 }}</span> sampai <span class="fw-bold text-gray-800">{{ $leads->lastItem() ?? 0 }}</span> dari total <span class="fw-bold text-gray-800">{{ $leads->total() }}</span> leads
                </div>
                <div>
                    {{ $leads->links('pagination::bootstrap-5') }}
                </div>
            </div>
            <!--end::Pagination Area-->

        </div>
    </div>
    <!--end::Main Card-->

</div>

<!--begin::Modal Push ke WA Blast-->
<div class="modal fade" id="kt_modal_push_blast" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <form id="push-blast-form" action="{{ route('scraper.leads.push-to-blast') }}" method="POST" class="modal-content">
            @csrf
            <div id="modal-blast-lead-inputs"></div>

            <div class="modal-header">
                <h3 class="fw-bold d-flex align-items-center">
                    <i class="ki-duotone ki-whatsapp fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                    Kirim Data Leads ke WA Blast
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body py-6 px-lg-8">
                <!-- Info banner pilihan -->
                <div class="alert alert-light-success d-flex align-items-center p-3 mb-5 rounded-3">
                    <i class="ki-duotone ki-information-5 fs-2 text-success me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <div class="fs-7 text-gray-800">
                        Saat ini Anda mencentang <span class="badge badge-success fw-bold fs-7 mx-1" id="modal-selected-badge">0</span> data lead di tabel.
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold text-gray-800 required">Pilihan Target Kontak:</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" name="send_all_new" value="0" id="target_selected" checked>
                            <label class="form-check-label fw-semibold text-gray-700" for="target_selected">
                                Hanya yang dicentang di tabel (<span id="modal-target-selected-count">0</span> leads)
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" name="send_all_new" value="1" id="target_all_new">
                            <label class="form-check-label fw-semibold text-gray-700" for="target_all_new">
                                Semua Lead Baru (Status: New) yang memiliki nomor WhatsApp
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold text-gray-800 required">Judul Campaign Blast:</label>
                    <input type="text" name="campaign_title" class="form-control" value="Penawaran Pembuatan Website Cafe" placeholder="Contoh: Promo Website Cafe Medan" required>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold text-gray-800 required">Pesan Penawaran Promosi:</label>
                    <textarea name="message" class="form-control" rows="6" required>Halo {nama}, kami dari MDT Developer.

Kami melihat usaha Anda di Google Maps sangat populer dengan ulasan pelanggan yang bagus! Namun kami perhatikan usaha Anda belum memiliki website profil / menu online sendiri.

Apakah Anda tertarik untuk meningkatkan penjualan dan kredibilitas dengan memiliki website resmi profesional? Kami menyediakan layanan pembuatan website kilat khusus UMKM lokal.

Boleh kami kirimkan portofolio contoh website yang pernah kami buat?</textarea>
                    <span class="text-muted fs-8 mt-1 d-block">
                        Variabel otomatis: <code>{nama}</code> untuk nama usaha/cafe, <code>{alamat}</code> untuk alamat.
                    </span>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold text-gray-800 required">Jeda Antar Pesan (Detik):</label>
                    <input type="number" name="delay_seconds" class="form-control" value="5" min="2" max="30" required>
                    <span class="text-muted fs-8">Disarankan minimal 5 detik agar nomor WA tetap aman dari risiko blokir.</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success fw-bold">
                    <i class="ki-duotone ki-check fs-4 me-1"></i> Buat Campaign & Masukkan Kontak
                </button>
            </div>
        </form>
    </div>
</div>
<!--end::Modal Push ke WA Blast-->

<!--begin::Hidden Form Bulk Delete-->
<form id="bulk-delete-form" action="{{ route('scraper.leads.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    <div id="bulk-delete-inputs"></div>
</form>

<!--begin::Hidden Form Single Delete-->
<form id="single-delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function getSelectedCheckboxes() {
        return Array.from(document.querySelectorAll('.lead-checkbox:checked'));
    }

    function updateSelectionUI() {
        const checked = getSelectedCheckboxes();
        const total = checked.length;
        const totalCheckboxes = document.querySelectorAll('.lead-checkbox').length;

        // Update all counters
        document.querySelectorAll('.selected-count-label').forEach(el => el.textContent = total);
        const badgeCount = document.getElementById('selected-badge-count');
        if (badgeCount) badgeCount.textContent = total;

        const modalBadge = document.getElementById('modal-selected-badge');
        if (modalBadge) modalBadge.textContent = total;

        const modalTargetCount = document.getElementById('modal-target-selected-count');
        if (modalTargetCount) modalTargetCount.textContent = total;

        // Top toolbar buttons
        const topBulkDelete = document.getElementById('btn-top-bulk-delete');
        if (topBulkDelete) {
            if (total > 0) {
                topBulkDelete.classList.remove('d-none');
            } else {
                topBulkDelete.classList.add('d-none');
            }
        }

        const topBlastCount = document.getElementById('top-blast-count');
        if (topBlastCount) {
            if (total > 0) {
                topBlastCount.textContent = total;
                topBlastCount.classList.remove('d-none');
            } else {
                topBlastCount.classList.add('d-none');
            }
        }

        // Floating action bar
        const actionBar = document.getElementById('bulk-action-bar');
        if (actionBar) {
            if (total > 0) {
                actionBar.classList.remove('d-none');
            } else {
                actionBar.classList.add('d-none');
            }
        }

        // Master checkbox state
        const checkAll = document.getElementById('check-all');
        if (checkAll && totalCheckboxes > 0) {
            checkAll.checked = total === totalCheckboxes;
            checkAll.indeterminate = total > 0 && total < totalCheckboxes;
        }

        // Highlight table rows
        document.querySelectorAll('.lead-checkbox').forEach(cb => {
            const tr = cb.closest('tr');
            if (tr) {
                if (cb.checked) {
                    tr.classList.add('table-active');
                } else {
                    tr.classList.remove('table-active');
                }
            }
        });
    }

    function toggleCheckAll(source) {
        const checkboxes = document.querySelectorAll('.lead-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateSelectionUI();
    }

    function deselectAll() {
        const checkboxes = document.querySelectorAll('.lead-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        const checkAll = document.getElementById('check-all');
        if (checkAll) checkAll.checked = false;
        updateSelectionUI();
    }

    function confirmBulkDelete() {
        const checked = getSelectedCheckboxes();
        if (checked.length === 0) {
            alert('Silakan pilih minimal satu lead UMKM yang ingin dihapus.');
            return;
        }

        const count = checked.length;
        const msg = `Apakah Anda yakin ingin menghapus ${count} data lead UMKM terpilih?\nTindakan ini tidak dapat dibatalkan.`;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus ' + count + ' Data Lead?',
                text: 'Data yang dihapus tidak dapat dipulihkan kembali!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitBulkDelete(checked);
                }
            });
        } else {
            if (confirm(msg)) {
                submitBulkDelete(checked);
            }
        }
    }

    function submitBulkDelete(checkedElements) {
        const container = document.getElementById('bulk-delete-inputs');
        container.innerHTML = '';
        checkedElements.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'lead_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        document.getElementById('bulk-delete-form').submit();
    }

    function openPushBlastModal() {
        const checked = getSelectedCheckboxes();
        const total = checked.length;

        // Siapkan input hidden di dalam modal form
        const container = document.getElementById('modal-blast-lead-inputs');
        container.innerHTML = '';
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'lead_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        // Tentukan radio default
        if (total > 0) {
            document.getElementById('target_selected').checked = true;
        } else {
            document.getElementById('target_all_new').checked = true;
        }

        updateSelectionUI();

        const modalEl = document.getElementById('kt_modal_push_blast');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    function deleteLead(id, name) {
        const msg = name ? `Yakin ingin menghapus data lead "${name}"?` : 'Yakin ingin menghapus data lead ini?';
        if (confirm(msg)) {
            const form = document.getElementById('single-delete-form');
            form.action = `{{ url('/scraper/leads') }}/${id}`;
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateSelectionUI();
    });
</script>
@endpush
@endsection
