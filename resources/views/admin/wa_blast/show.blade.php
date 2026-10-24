@extends('layouts.admin')

@section('page_title', 'Eksekusi & Monitoring WA Blast')

@section('content')
<div class="d-flex flex-column gap-5">
    <!--begin::Header Navigation-->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold text-gray-900 mb-0">{{ $campaign->title }}</h3>
                <span id="campaign-status-badge" class="badge {{ $campaign->status === 'completed' ? 'badge-light-success text-success' : ($campaign->status === 'processing' ? 'badge-light-primary text-primary' : 'badge-light-warning text-warning') }} fw-bold px-3 py-1">
                    {{ ucfirst($campaign->status) }}
                </span>
            </div>
            <div class="text-muted fs-7 mt-1">
                Dibuat pada {{ $campaign->created_at->format('d M Y, H:i') }} WIB 
                • Jeda: <span class="fw-bold text-gray-800">{{ $campaign->delay_seconds }} detik/pesan</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('wa-blast.index') }}" class="btn btn-sm btn-light">
                <i class="ki-duotone ki-arrow-left fs-4 me-1"></i> Kembali ke Daftar
            </a>
            <form action="{{ route('wa-blast.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kampanye blast ini?');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-light-danger" title="Hapus Kampanye">
                    <i class="ki-duotone ki-trash fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> Hapus
                </button>
            </form>
        </div>
    </div>
    <!--end::Header Navigation-->

    <!--begin::Gateway Status Alert if not connected-->
    @if(($status['status'] ?? '') !== 'connected')
        <div class="alert alert-warning d-flex align-items-center justify-content-between p-4 border border-warning">
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div>
                    <h5 class="fw-bold text-gray-900 mb-1">Peringatan: WhatsApp Gateway Belum Terhubung!</h5>
                    <div class="text-muted fs-7">Status gateway saat ini adalah <strong>{{ $status['status'] ?? 'disconnected' }}</strong>. Mohon hubungkan sesi WhatsApp Anda terlebih dahulu agar pesan dapat terkirim.</div>
                </div>
            </div>
            <a href="{{ route('wa-chat.index') }}" class="btn btn-sm btn-warning text-white fw-bold">
                Buka Live Chat & Pindai QR
            </a>
        </div>
    @endif
    <!--end::Gateway Status Alert-->

    <!--begin::Live Progress Bar & Stats-->
    <div class="card shadow-sm border-0">
        <div class="card-body py-5">
            <div class="row g-4 mb-4">
                <div class="col-sm-3 col-6">
                    <div class="border rounded p-3 text-center bg-light-subtle">
                        <div class="text-muted fs-8 fw-bold text-uppercase">Total Target</div>
                        <div class="fs-2x fw-bold text-gray-900" id="stat-total">{{ $campaign->total_recipients }}</div>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="border rounded p-3 text-center bg-light-success">
                        <div class="text-success fs-8 fw-bold text-uppercase">Berhasil Terkirim</div>
                        <div class="fs-2x fw-bold text-success" id="stat-sent">{{ $campaign->sent_count }}</div>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="border rounded p-3 text-center bg-light-danger">
                        <div class="text-danger fs-8 fw-bold text-uppercase">Gagal</div>
                        <div class="fs-2x fw-bold text-danger" id="stat-failed">{{ $campaign->failed_count }}</div>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="border rounded p-3 text-center bg-light-warning">
                        <div class="text-warning fs-8 fw-bold text-uppercase">Menunggu Antrean</div>
                        <div class="fs-2x fw-bold text-warning" id="stat-pending">{{ $campaign->recipients->where('status', 'pending')->count() }}</div>
                    </div>
                </div>
            </div>

            @php
                $initialPercent = $campaign->total_recipients > 0 ? round(($campaign->sent_count / $campaign->total_recipients) * 100) : 0;
            @endphp
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold fs-7 text-gray-800">Progres Pengiriman Promosi:</span>
                <span class="fw-bold fs-7 text-primary" id="progress-percent-text">{{ $initialPercent }}%</span>
            </div>
            <div class="progress h-15px w-100 bg-light rounded-pill overflow-hidden">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="progress-bar-el" role="progressbar" style="width: {{ $initialPercent }}%" aria-valuenow="{{ $initialPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <!--Execution Controls-->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-5 pt-3 border-top">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-success fw-bold px-5 py-3 d-flex align-items-center gap-2" id="btn-start-blast">
                        <i class="ki-duotone ki-send fs-2"><span class="path1"></span><span class="path2"></span></i>
                        <span id="btn-start-text">Mulai Pengiriman Blast</span>
                    </button>

                    <button type="button" class="btn btn-warning fw-bold px-4 py-3 d-none align-items-center gap-2" id="btn-pause-blast">
                        <i class="ki-duotone ki-pause-circle fs-2"><span class="path1"></span><span class="path2"></span></i>
                        <span>Jeda / Pause</span>
                    </button>
                </div>

                <div class="text-muted fs-8">
                    Setiap pesan akan dikirim otomatis dengan jeda <strong>{{ $campaign->delay_seconds }} detik</strong> per nomor. Jangan tutup tab browser selama pengiriman berlangsung.
                </div>
            </div>
        </div>
    </div>
    <!--end::Live Progress Bar-->

    <!--begin::Live Transmission Terminal Log-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 py-3 bg-dark">
            <h5 class="card-title text-white fw-bold m-0 fs-7">
                <i class="ki-duotone ki-code text-success fs-4 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                Log Transmisi Pengiriman Realtime
            </h5>
            <button type="button" class="btn btn-xs btn-outline btn-outline-secondary text-white py-1 px-2 fs-9" id="btn-clear-log">Bersihkan Log</button>
        </div>
        <div class="card-body p-3 bg-black" style="max-height: 180px; overflow-y: auto;" id="blast-console-log">
            <div class="font-monospace fs-8 text-success-emphasis" id="log-content">
                <div>[{{ now()->format('H:i:s') }}] Siap mengirim pesan promosi. Tekan tombol 'Mulai Pengiriman Blast' untuk memulai.</div>
            </div>
        </div>
    </div>
    <!--end::Live Transmission Log-->

    <!--begin::Campaign Message Preview Accordion-->
    <div class="card shadow-sm border-0">
        <div class="card-body py-4">
            <div class="d-flex align-items-center justify-content-between cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapse-message-template">
                <div class="fw-bold text-gray-900 fs-6">
                    <i class="ki-duotone ki-document text-primary fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                    Lihat Template Pesan yang Dikirim
                </div>
                <i class="ki-duotone ki-down fs-4 text-gray-500"></i>
            </div>
            <div class="collapse mt-3" id="collapse-message-template">
                <div class="p-3 bg-light rounded border text-gray-800 fs-7 font-monospace" style="white-space: pre-wrap;">{{ $campaign->message }}</div>
            </div>
        </div>
    </div>
    <!--end::Campaign Message Preview-->

    <!--begin::Recipients Table-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h4 class="fw-bold text-gray-900 m-0">Daftar Penerima Kampanye</h4>
            </div>
            <div class="card-toolbar d-flex align-items-center gap-3">
                <div class="position-relative">
                    <i class="ki-duotone ki-magnifier fs-4 text-gray-500 position-absolute top-50 translate-middle-y ms-3"></i>
                    <input type="text" id="filter-recipient-input" class="form-control form-control-sm form-control-solid ps-9" placeholder="Cari nama atau nomor..." />
                </div>
                <select id="filter-status-select" class="form-select form-select-sm form-select-solid" style="width: 140px;">
                    <option value="all">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="sent">Terkirim</option>
                    <option value="failed">Gagal</option>
                </select>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-7 gy-4" id="table-recipients">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                            <th class="w-10px">No</th>
                            <th class="min-w-150px">Nama Penerima</th>
                            <th class="min-w-125px">Nomor WhatsApp</th>
                            <th class="min-w-100px text-center">Status</th>
                            <th class="min-w-100px">Waktu Kirim</th>
                            <th class="min-w-150px">Keterangan / Error</th>
                            <th class="text-end min-w-80px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach($campaign->recipients as $idx => $recipient)
                            <tr class="recipient-row" 
                                id="recipient-row-{{ $recipient->id }}" 
                                data-id="{{ $recipient->id }}" 
                                data-status="{{ $recipient->status }}"
                                data-phone="{{ $recipient->phone_number }}"
                                data-name="{{ $recipient->recipient_name }}">
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-bold text-gray-900 col-name">{{ $recipient->recipient_name ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-light-primary fw-bold font-monospace col-phone">{{ $recipient->phone_number }}</span>
                                </td>
                                <td class="text-center col-status">
                                    @if($recipient->status === 'sent')
                                        <span class="badge badge-light-success fw-bold">Terkirim</span>
                                    @elseif($recipient->status === 'failed')
                                        <span class="badge badge-light-danger fw-bold">Gagal</span>
                                    @else
                                        <span class="badge badge-light-secondary fw-bold">Menunggu</span>
                                    @endif
                                </td>
                                <td class="col-time">{{ $recipient->sent_at ? $recipient->sent_at->format('H:i:s') : '-' }}</td>
                                <td class="col-error">
                                    @if($recipient->error_message)
                                        <span class="text-danger fs-8" title="{{ $recipient->error_message }}">{{ Str::limit($recipient->error_message, 40) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-xs btn-icon btn-light-success btn-retry-single" data-id="{{ $recipient->id }}" title="Kirim Sekarang">
                                        <i class="ki-duotone ki-send fs-6"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::Recipients Table-->
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const campaignId = {{ $campaign->id }};
    const delaySeconds = {{ $campaign->delay_seconds }};
    let isRunning = false;
    let isPaused = false;

    const btnStart = document.getElementById('btn-start-blast');
    const btnPause = document.getElementById('btn-pause-blast');
    const startText = document.getElementById('btn-start-text');
    const progressBar = document.getElementById('progress-bar-el');
    const progressPercentText = document.getElementById('progress-percent-text');
    const statSent = document.getElementById('stat-sent');
    const statFailed = document.getElementById('stat-failed');
    const statPending = document.getElementById('stat-pending');
    const logContainer = document.getElementById('blast-console-log');
    const logContent = document.getElementById('log-content');

    function log(message, type = 'normal') {
        const time = new Date().toLocaleTimeString('id-ID');
        const line = document.createElement('div');
        if (type === 'success') {
            line.className = 'text-success';
        } else if (type === 'danger') {
            line.className = 'text-danger';
        } else if (type === 'warning') {
            line.className = 'text-warning';
        } else {
            line.className = 'text-gray-400';
        }
        line.innerHTML = `[${time}] ${message}`;
        logContent.appendChild(line);
        logContainer.scrollTop = logContainer.scrollHeight;
    }

    document.getElementById('btn-clear-log').addEventListener('click', () => {
        logContent.innerHTML = '';
    });

    // 1. Single send AJAX request
    async function sendSingleRecipient(recipientId) {
        const res = await fetch(`{{ url('wa-blast') }}/${campaignId}/send/${recipientId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        return await res.json();
    }

    // Update row DOM
    function updateRowUI(recipient) {
        const row = document.getElementById(`recipient-row-${recipient.id}`);
        if (!row) return;

        row.setAttribute('data-status', recipient.status);

        const statusCell = row.querySelector('.col-status');
        const timeCell = row.querySelector('.col-time');
        const errorCell = row.querySelector('.col-error');

        if (recipient.status === 'sent') {
            statusCell.innerHTML = '<span class="badge badge-light-success fw-bold">Terkirim</span>';
            timeCell.textContent = recipient.sent_at || new Date().toLocaleTimeString('id-ID');
            errorCell.innerHTML = '<span class="text-muted">-</span>';
        } else if (recipient.status === 'failed') {
            statusCell.innerHTML = '<span class="badge badge-light-danger fw-bold">Gagal</span>';
            timeCell.textContent = recipient.sent_at || new Date().toLocaleTimeString('id-ID');
            errorCell.innerHTML = `<span class="text-danger fs-8" title="${recipient.error_message || ''}">${(recipient.error_message || 'Gagal').substring(0, 40)}</span>`;
        }
    }

    function updateStatsUI(campaign) {
        statSent.textContent = campaign.sent_count;
        statFailed.textContent = campaign.failed_count;
        statPending.textContent = campaign.remaining;

        const total = campaign.total_recipients || 1;
        const percent = Math.round((campaign.sent_count / total) * 100);

        progressBar.style.width = percent + '%';
        progressPercentText.textContent = percent + '%';

        const statusBadge = document.getElementById('campaign-status-badge');
        if (campaign.status === 'completed') {
            statusBadge.className = 'badge badge-light-success text-success fw-bold px-3 py-1';
            statusBadge.textContent = 'Completed';
        } else if (campaign.status === 'processing') {
            statusBadge.className = 'badge badge-light-primary text-primary fw-bold px-3 py-1';
            statusBadge.textContent = 'Processing';
        }
    }

    // 2. Loop Execution
    async function startBlastProcess() {
        isRunning = true;
        isPaused = false;

        btnStart.classList.add('d-none');
        btnPause.classList.remove('d-none');

        log(`🚀 Memulai pengiriman kampanye blast... Jeda ${delaySeconds} detik per pesan.`, 'warning');

        // Find all pending rows
        const rows = Array.from(document.querySelectorAll('.recipient-row[data-status="pending"]'));

        if (rows.length === 0) {
            log('Tidak ada nomor dengan status antrean (pending). Semua sudah diproses!', 'warning');
            finishBlast(true);
            return;
        }

        for (let i = 0; i < rows.length; i++) {
            if (isPaused) {
                log('⏸ Pengiriman dijeda oleh pengguna.', 'warning');
                break;
            }

            const row = rows[i];
            const recipientId = row.getAttribute('data-id');
            const phone = row.getAttribute('data-phone');
            const name = row.getAttribute('data-name') || phone;

            log(`Mengirim ke ${phone} (${name})...`);

            try {
                const data = await sendSingleRecipient(recipientId);

                if (data.recipient) {
                    updateRowUI(data.recipient);
                }
                if (data.campaign) {
                    updateStatsUI(data.campaign);
                }

                if (data.success) {
                    log(`✓ Terkirim ke ${phone}`, 'success');
                } else {
                    log(`✗ Gagal kirim ke ${phone}: ${data.message || 'Error'}`, 'danger');
                }
            } catch (err) {
                log(`✗ Terjadi error koneksi pada ${phone}: ${err.message}`, 'danger');
            }

            // Sleep for delay seconds if not the last item and not paused
            if (i < rows.length - 1 && !isPaused) {
                log(`Menunggu jeda ${delaySeconds} detik...`);
                await new Promise(resolve => setTimeout(resolve, delaySeconds * 1000));
            }
        }

        finishBlast();
    }

    function finishBlast(allDone = false) {
        isRunning = false;
        btnStart.classList.remove('d-none');
        btnPause.classList.add('d-none');

        const remaining = document.querySelectorAll('.recipient-row[data-status="pending"]').length;
        if (remaining === 0 || allDone) {
            startText.textContent = 'Semua Terkirim';
            btnStart.disabled = true;
            log('🎉 Seluruh antrean pesan WA Blast telah selesai diproses!', 'success');
            Swal.fire('Selesai!', 'Seluruh nomor target telah selesai diproses.', 'success');
        } else {
            startText.textContent = 'Lanjutkan Pengiriman';
            btnStart.disabled = false;
        }
    }

    btnStart.addEventListener('click', function () {
        if (!isRunning) {
            startBlastProcess();
        }
    });

    btnPause.addEventListener('click', function () {
        isPaused = true;
        isRunning = false;
        btnStart.classList.remove('d-none');
        btnPause.classList.add('d-none');
        startText.textContent = 'Lanjutkan Pengiriman';
    });

    // 3. Retry Single Recipient
    document.querySelectorAll('.btn-retry-single').forEach(btn => {
        btn.addEventListener('click', async function () {
            const recipientId = this.getAttribute('data-id');
            const btnEl = this;
            btnEl.disabled = true;
            btnEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            log(`Mencoba kirim ulang ke ID #${recipientId}...`);

            try {
                const data = await sendSingleRecipient(recipientId);
                btnEl.disabled = false;
                btnEl.innerHTML = '<i class="ki-duotone ki-send fs-6"><span class="path1"></span><span class="path2"></span></i>';

                if (data.recipient) {
                    updateRowUI(data.recipient);
                }
                if (data.campaign) {
                    updateStatsUI(data.campaign);
                }

                if (data.success) {
                    log(`✓ Berhasil dikirim ulang ke ID #${recipientId}`, 'success');
                    toastr.success('Pesan berhasil dikirim!');
                } else {
                    log(`✗ Gagal mengirim ke ID #${recipientId}: ${data.message}`, 'danger');
                    toastr.error(data.message || 'Gagal mengirim');
                }
            } catch (err) {
                btnEl.disabled = false;
                btnEl.innerHTML = '<i class="ki-duotone ki-send fs-6"><span class="path1"></span><span class="path2"></span></i>';
                toastr.error('Error: ' + err.message);
            }
        });
    });

    // 4. Search & Filter Table
    const searchInput = document.getElementById('filter-recipient-input');
    const statusSelect = document.getElementById('filter-status-select');

    function applyFilter() {
        const query = searchInput.value.toLowerCase();
        const selectedStatus = statusSelect.value;

        document.querySelectorAll('.recipient-row').forEach(row => {
            const name = row.querySelector('.col-name').textContent.toLowerCase();
            const phone = row.querySelector('.col-phone').textContent.toLowerCase();
            const status = row.getAttribute('data-status');

            const matchesSearch = name.includes(query) || phone.includes(query);
            const matchesStatus = (selectedStatus === 'all' || status === selectedStatus);

            if (matchesSearch && matchesStatus) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });
    }

    searchInput.addEventListener('input', applyFilter);
    statusSelect.addEventListener('change', applyFilter);
});
</script>
@endpush
@endsection
