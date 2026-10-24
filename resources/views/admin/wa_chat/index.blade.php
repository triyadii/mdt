@extends('layouts.admin')

@section('page_title', 'WhatsApp Live Chat')

@section('content')
<div class="d-flex flex-column gap-5">
    <!--begin::Connection Status Bar-->
    <div class="card shadow-sm border-0">
        <div class="card-body py-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-45px symbol-circle bg-light-success d-flex align-items-center justify-content-center">
                        <i class="ki-duotone ki-whatsapp fs-2x text-success">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fw-bold mb-0 text-gray-900">WhatsApp Gateway MDT</h4>
                            <span id="gateway-status-badge" class="badge {{ ($status['status'] ?? '') === 'connected' ? 'badge-light-success text-success' : (($status['status'] ?? '') === 'qr_ready' ? 'badge-light-warning text-warning' : 'badge-light-danger text-danger') }} fw-bold px-3 py-2">
                                <span class="spinner-grow spinner-grow-sm me-1 align-middle" role="status" aria-hidden="true" style="width: 8px; height: 8px;"></span>
                                <span id="gateway-status-text">
                                    @if(($status['status'] ?? '') === 'connected')
                                        Terhubung (Online)
                                    @elseif(($status['status'] ?? '') === 'qr_ready')
                                        Menunggu Scan QR
                                    @else
                                        Terputus (Disconnected)
                                    @endif
                                </span>
                            </span>
                        </div>
                        <div class="text-muted fs-7 mt-1">
                            Sesi: <span class="fw-bold text-gray-800" id="session-id-text">{{ $status['sessionId'] ?? 'MDT' }}</span>
                            <span class="mx-2">•</span>
                            Target API: <span class="text-primary">{{ config('services.wa_gateway.base_url') }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btn-check-status">
                        <i class="ki-duotone ki-arrows-circle fs-3 me-1"><span class="path1"></span><span class="path2"></span></i>
                        Periksa Status
                    </button>

                    <button type="button" class="btn btn-sm btn-light-success fw-bold {{ ($status['status'] ?? '') === 'qr_ready' ? '' : 'd-none' }}" id="btn-open-qr" data-bs-toggle="modal" data-bs-target="#modal-scan-qr">
                        <i class="ki-duotone ki-scan-barcode fs-3 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></i>
                        Pindai QR Code
                    </button>

                    <button type="button" class="btn btn-sm btn-success fw-bold {{ ($status['status'] ?? '') === 'connected' ? 'd-none' : '' }}" id="btn-start-session">
                        <i class="ki-duotone ki-send fs-3 me-1"><span class="path1"></span><span class="path2"></span></i>
                        Hubungkan WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Connection Status Bar-->

    <!--begin::Chat Container-->
    <div class="card shadow-sm border-0 overflow-hidden" style="min-height: 650px;">
        <div class="card-body p-0">
            <div class="row g-0 h-100" style="min-height: 650px;">
                <!--begin::Left Sidebar: Contacts & Chat List-->
                <div class="col-lg-4 col-xl-3 border-end d-flex flex-column bg-light-subtle">
                    <!--Search & New Chat Bar-->
                    <div class="p-4 border-bottom bg-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold m-0 text-gray-800">Daftar Obrolan</h5>
                            <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1 px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modal-new-chat">
                                <i class="ki-duotone ki-plus fs-4"></i>
                                <span>Nomor Baru</span>
                            </button>
                        </div>
                        <div class="position-relative">
                            <i class="ki-duotone ki-magnifier fs-3 text-gray-500 position-absolute top-50 translate-middle-y ms-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" id="chat-search-input" class="form-control form-control-sm form-control-solid ps-10" placeholder="Cari nama atau nomor..." />
                        </div>
                    </div>

                    <!--Scrollable Chat List-->
                    <div class="flex-grow-1 overflow-auto" id="chat-list-container" style="max-height: 540px;">
                        @forelse($localChats as $chat)
                            <div class="chat-list-item p-3 border-bottom cursor-pointer d-flex align-items-center justify-content-between transition-all" 
                                 data-chat-id="{{ $chat->id }}"
                                 data-number="{{ $chat->number }}"
                                 data-name="{{ $chat->name ?: $chat->number }}">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="wa-avatar flex-shrink-0">
                                        {{ strtoupper(substr($chat->name ?: $chat->number, 0, 2)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-gray-900 text-truncate contact-name">{{ $chat->name ?: $chat->number }}</div>
                                        <div class="text-muted fs-8 text-truncate contact-number font-monospace">{{ $chat->number }}</div>
                                        <div class="text-gray-600 fs-7 text-truncate contact-last-message mt-1" style="max-width: 160px;">
                                            {{ $chat->last_message ?: 'Belum ada percakapan' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end justify-content-between flex-shrink-0 ms-2" style="min-height: 40px;">
                                    <div class="text-muted fs-8 contact-time">{{ $chat->last_message_at ? $chat->last_message_at->diffForHumans(null, true) : '' }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        @if($chat->unread_count > 0)
                                            <span class="badge badge-circle badge-success">{{ $chat->unread_count }}</span>
                                        @endif
                                        <button type="button" class="btn btn-icon btn-xs btn-active-light-danger btn-delete-chat text-muted p-0" data-chat-id="{{ $chat->id }}" data-number="{{ $chat->number }}" title="Hapus Obrolan">
                                            <i class="ki-duotone ki-trash fs-6"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 px-4 text-muted" id="chat-list-empty">
                                <i class="ki-duotone ki-message-text fs-3x text-gray-400 mb-3 d-block"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                <div class="fw-semibold">Belum ada percakapan aktif.</div>
                                <div class="fs-8 mt-1">Klik tombol <strong>Nomor Baru</strong> di atas untuk memulai chat.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <!--end::Left Sidebar-->

                <!--begin::Right: Active Conversation Window-->
                <div class="col-lg-8 col-xl-9 d-flex flex-column bg-body">
                    <!--Empty State (When no chat selected)-->
                    <div id="chat-window-empty" class="d-flex flex-column align-items-center justify-content-center h-100 py-15 text-center">
                        <div class="symbol symbol-100px symbol-circle bg-light-success p-4 mb-4">
                            <i class="ki-duotone ki-whatsapp fs-5x text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <h3 class="fw-bold text-gray-800 mb-2">WhatsApp Web Live Chat</h3>
                        <p class="text-muted fs-6 mb-4" style="max-width: 420px;">
                            Pilih salah satu kontak di bilah kiri untuk membuka riwayat pesan, atau klik tombol <strong>Nomor Baru</strong> untuk memulai obrolan WhatsApp baru.
                        </p>
                        <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modal-new-chat">
                            <i class="ki-duotone ki-plus fs-3 me-1"></i> Mulai Chat Baru
                        </button>
                    </div>

                    <!--Active Chat Content (Hidden by default)-->
                    <div id="chat-window-active" class="d-none flex-column h-100">
                        <!--Chat Header-->
                        <div class="p-3 px-4 border-bottom d-flex align-items-center justify-content-between bg-light-subtle">
                            <div class="d-flex align-items-center gap-3">
                                <div class="wa-avatar-lg flex-shrink-0" id="active-chat-avatar">
                                    WA
                                </div>
                                <div>
                                    <div class="fw-bold text-gray-900 fs-6" id="active-chat-name">Penerima</div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted fs-7 font-monospace" id="active-chat-number">628...</span>
                                        <a href="#" id="active-chat-wa-link" target="_blank" class="badge badge-light-success fs-8 text-decoration-none">
                                            <i class="ki-duotone ki-exit-right-corner fs-8 me-1"></i>Buka di WA
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-icon btn-sm btn-light-primary" id="btn-refresh-messages" title="Segarkan pesan">
                                    <i class="ki-duotone ki-arrows-circle fs-3"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger fw-bold d-flex align-items-center gap-1" id="btn-delete-active-chat" title="Hapus Obrolan Ini">
                                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    <span class="d-none d-sm-inline">Hapus Obrolan</span>
                                </button>
                            </div>
                        </div>

                        <!--Messages Scroll Area-->
                        <div class="flex-grow-1 p-4 overflow-auto" id="messages-container" style="height: 440px; background-color: #efeae2; background-image: radial-gradient(#d1d7db 1px, transparent 1px); background-size: 20px 20px;">
                            <div id="messages-list" class="d-flex flex-column gap-3">
                                <!-- Messages will be dynamically rendered here -->
                            </div>
                        </div>

                        <!--Quick Reply Templates-->
                        <div class="px-4 py-2 border-top bg-light-subtle d-flex align-items-center gap-2 overflow-auto">
                            <span class="text-muted fs-8 fw-bold flex-shrink-0">Template Cepat:</span>
                            <button type="button" class="btn btn-sm btn-light btn-active-light-primary py-1 px-3 fs-8 text-nowrap quick-reply-btn" data-text="Halo kak, terima kasih sudah menghubungi MDT Solution. Ada yang bisa kami bantu hari ini?">
                                👋 Sapaan Hangat
                            </button>
                            <button type="button" class="btn btn-sm btn-light btn-active-light-primary py-1 px-3 fs-8 text-nowrap quick-reply-btn" data-text="Baik kak, permohonan konsultasi Anda sudah kami jadwalkan bersama tim teknis MDT Solution.">
                                📅 Jadwal Konsultasi
                            </button>
                            <button type="button" class="btn btn-sm btn-light btn-active-light-primary py-1 px-3 fs-8 text-nowrap quick-reply-btn" data-text="Terima kasih atas kerja samanya kak! Jika butuh bantuan pengembangan sistem/website lainnya jangan ragu kabari kami ya.">
                                ✨ Ucapan Terima Kasih
                            </button>
                        </div>

                        <!--Message Input Box-->
                        <div class="p-3 border-top bg-body">
                            <form id="form-send-message" method="POST" action="javascript:void(0);" onsubmit="return false;" class="d-flex align-items-end gap-2">
                                @csrf
                                <input type="hidden" id="active-chat-target-number" name="number" value="">
                                <input type="hidden" id="active-chat-target-name" name="name" value="">
                                <div class="flex-grow-1">
                                    <textarea id="message-input" name="message" class="form-control form-control-solid border-0" rows="2" placeholder="Ketik pesan WhatsApp di sini... (Tekan Enter untuk kirim, Shift+Enter untuk baris baru)" style="resize: none;"></textarea>
                                </div>
                                <button type="button" class="btn btn-success fw-bold px-4 py-3 d-flex align-items-center gap-2 flex-shrink-0" id="btn-send-message">
                                    <span>Kirim</span>
                                    <i class="ki-duotone ki-send fs-3"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!--end::Right Window-->
            </div>
        </div>
    </div>
    <!--end::Chat Container-->
</div>

<!--begin::Modal Scan QR Code-->
<div class="modal fade" id="modal-scan-qr" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
            <div class="modal-body text-center px-5 pb-5">
                <div class="symbol symbol-60px symbol-circle bg-light-success p-3 mb-3">
                    <i class="ki-duotone ki-whatsapp fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <h3 class="fw-bold text-gray-900 mb-1">Pindai Kode QR WhatsApp</h3>
                <div class="text-muted fs-7 mb-4">Gunakan aplikasi WhatsApp di ponsel Anda untuk menghubungkan akun.</div>

                <div class="bg-white border rounded p-3 d-inline-block shadow-sm mb-4" style="min-width: 260px; min-height: 260px;">
                    <div id="qr-loading-placeholder" class="py-12 {{ !empty($status['qr']) ? 'd-none' : '' }}">
                        <div class="spinner-border text-success mb-3" role="status"></div>
                        <div class="text-muted fs-7">Membuat kode QR WhatsApp...</div>
                    </div>
                    <img id="qr-code-img" src="{{ $status['qr'] ?? '' }}" alt="WhatsApp QR Code" class="img-fluid rounded {{ !empty($status['qr']) ? '' : 'd-none' }}" style="max-width: 250px; max-height: 250px;" />
                </div>

                <div class="alert alert-light-primary text-start fs-7 p-3 mb-4">
                    <ol class="mb-0 ps-3">
                        <li>Buka WhatsApp di ponsel Anda</li>
                        <li>Ketuk <strong>Menu (⋮)</strong> atau <strong>Pengaturan</strong> lalu pilih <strong>Perangkat Tertaut</strong></li>
                        <li>Ketuk <strong>Tautkan Perangkat</strong></li>
                        <li>Arahkan kamera ke kode QR di atas</li>
                    </ol>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="btn-refresh-qr">
                        <i class="ki-duotone ki-arrows-circle fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> Perbarui QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Scan QR Code-->

<!--begin::Modal New Chat / Tambah Nomor-->
<div class="modal fade" id="modal-new-chat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="fw-bold modal-title text-gray-900">
                    <i class="ki-duotone ki-whatsapp text-success fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>
                    Mulai Chat WhatsApp Baru
                </h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
            <form id="form-new-chat" method="POST" action="javascript:void(0);" onsubmit="return false;">
                @csrf
                <div class="modal-body py-4">
                    <!--Quick Select from Permohonan-->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-gray-700">Pilih Cepat dari Data Permohonan (Opsional):</label>
                        <select class="form-select form-select-solid" id="select-permohonan-contact">
                            <option value="">-- Pilih Kontak Pemohon --</option>
                            @foreach($permohonanContacts as $contact)
                                <option value="{{ $contact->nomorTelepon }}" data-name="{{ $contact->namaPermohonan }}" data-jasa="{{ $contact->jenisJasa }}">
                                    {{ $contact->namaPermohonan }} ({{ $contact->nomorTelepon }}) - {{ $contact->jenisJasa ?: 'Umum' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">Memilih kontak akan otomatis mengisi nomor dan nama di bawah.</div>
                    </div>

                    <div class="separator my-4"></div>

                    <!--Phone Number Input-->
                    <div class="mb-4">
                        <label class="form-label required fw-bold text-gray-700">Nomor WhatsApp Tujuan:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-primary text-primary fw-bold">+62 / 08</span>
                            <input type="text" name="number" id="new-chat-number" class="form-control" placeholder="Contoh: 081234567890 atau 6281234567890" required />
                        </div>
                        <div class="text-muted fs-8 mt-1">Format bebas (0812..., 62812..., atau +62812...). Sistem otomatis memformat ke standar WhatsApp.</div>
                    </div>

                    <!--Name Input-->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-gray-700">Nama Kontak (Opsional):</label>
                        <input type="text" name="name" id="new-chat-name" class="form-control" placeholder="Contoh: Budi Santoso / PT Sejahtera" />
                    </div>

                    <!--Initial Message-->
                    <div class="mb-3">
                        <label class="form-label required fw-bold text-gray-700">Pesan Awal:</label>
                        <textarea name="message" id="new-chat-message" class="form-control" rows="3" placeholder="Ketik pesan pembuka untuk dikirimkan ke nomor ini..." required>Halo kak, terima kasih sudah menghubungi MDT Solution Indonesia. Ada yang bisa kami bantu?</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success fw-bold" id="btn-submit-new-chat">
                        <i class="ki-duotone ki-send fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> Kirim Pesan & Buka Chat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal New Chat-->

<style>
.wa-avatar {
    width: 42px !important;
    height: 42px !important;
    min-width: 42px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    letter-spacing: 0.5px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(18, 140, 126, 0.25);
    user-select: none;
    line-height: 1;
}
.wa-avatar-lg {
    width: 44px !important;
    height: 44px !important;
    min-width: 44px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 700 !important;
    font-size: 1.05rem !important;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(18, 140, 126, 0.25);
    user-select: none;
    line-height: 1;
}
.chat-list-item {
    transition: all 0.2s ease;
    border-left: 3px solid transparent !important;
    position: relative;
}
.chat-list-item:hover {
    background-color: rgba(37, 211, 102, 0.08) !important;
}
.chat-list-item.active {
    background-color: rgba(37, 211, 102, 0.16) !important;
    border-left: 3px solid #25D366 !important;
}
[data-bs-theme="dark"] .chat-list-item.active {
    background-color: rgba(37, 211, 102, 0.22) !important;
    border-left: 3px solid #25D366 !important;
}
.chat-list-item.active .contact-name {
    color: #25D366 !important;
}
.chat-list-item .btn-delete-chat {
    opacity: 0;
    transition: opacity 0.2s ease;
    width: 22px;
    height: 22px;
}
.chat-list-item:hover .btn-delete-chat {
    opacity: 0.6;
}
.chat-list-item .btn-delete-chat:hover {
    opacity: 1 !important;
    color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.12) !important;
}

.chat-bubble {
    max-width: 75%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 0.925rem;
    line-height: 1.45;
    word-break: break-word;
    position: relative;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06);
}
.chat-bubble.outgoing {
    align-self: flex-end;
    background-color: #d9fdd3;
    color: #111b21;
    border-bottom-right-radius: 2px;
}
.chat-bubble.incoming {
    align-self: flex-start;
    background-color: #ffffff;
    color: #111b21;
    border-bottom-left-radius: 2px;
}
[data-bs-theme="dark"] .chat-bubble.outgoing {
    background-color: #005c4b !important;
    color: #e9edef !important;
}
[data-bs-theme="dark"] .chat-bubble.incoming {
    background-color: #202c33 !important;
    color: #e9edef !important;
}
[data-bs-theme="dark"] #messages-container {
    background-color: #0b141a !important;
    background-image: radial-gradient(#222e35 1px, transparent 1px) !important;
}
.chat-bubble .btn-delete-msg {
    opacity: 0;
    transition: all 0.2s ease;
    line-height: 1;
    width: 18px;
    height: 18px;
    border-radius: 50%;
}
.chat-bubble:hover .btn-delete-msg {
    opacity: 0.45;
}
.chat-bubble .btn-delete-msg:hover {
    opacity: 1 !important;
    color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.15) !important;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let currentChatNumber = '';
    let currentChatName = '';
    let currentChatId = '';
    let statusPollingTimer = null;

    // 1. Check Status Function
    async function checkStatus(silent = false) {
        try {
            const res = await fetch("{{ route('wa-chat.status') }}");
            const data = await res.json();
            
            const badge = document.getElementById('gateway-status-badge');
            const text = document.getElementById('gateway-status-text');
            const btnOpenQr = document.getElementById('btn-open-qr');
            const btnStart = document.getElementById('btn-start-session');
            const qrImg = document.getElementById('qr-code-img');
            const qrLoading = document.getElementById('qr-loading-placeholder');

            if (data.status === 'connected') {
                badge.className = 'badge badge-light-success text-success fw-bold px-3 py-2';
                text.textContent = 'Terhubung (Online)';
                btnOpenQr.classList.add('d-none');
                btnStart.classList.add('d-none');

                // If QR modal is open, close it and show success
                const qrModal = bootstrap.Modal.getInstance(document.getElementById('modal-scan-qr'));
                if (qrModal) {
                    qrModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'WhatsApp Terhubung!',
                        text: 'Gateway WhatsApp Anda kini telah aktif dan siap digunakan.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            } else if (data.status === 'qr_ready') {
                badge.className = 'badge badge-light-warning text-warning fw-bold px-3 py-2';
                text.textContent = 'Menunggu Scan QR';
                btnOpenQr.classList.remove('d-none');
                btnStart.classList.add('d-none');

                if (data.qr) {
                    qrImg.src = data.qr;
                    qrImg.classList.remove('d-none');
                    qrLoading.classList.add('d-none');
                }
            } else {
                badge.className = 'badge badge-light-danger text-danger fw-bold px-3 py-2';
                text.textContent = 'Terputus (Disconnected)';
                btnOpenQr.classList.add('d-none');
                btnStart.classList.remove('d-none');
            }

            if (!silent) {
                toastr.info('Status WhatsApp: ' + (data.status || 'unknown'));
            }

            return data;
        } catch (err) {
            console.error('Status check error:', err);
        }
    }

    document.getElementById('btn-check-status').addEventListener('click', () => checkStatus(false));
    document.getElementById('btn-refresh-qr').addEventListener('click', () => checkStatus(false));

    // 2. Start Session
    document.getElementById('btn-start-session').addEventListener('click', async function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghubungkan...';

        try {
            const res = await fetch("{{ route('wa-chat.start') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();
            
            // Poll for QR after starting
            setTimeout(async () => {
                const statusData = await checkStatus(true);
                btn.disabled = false;
                btn.innerHTML = '<i class="ki-duotone ki-send fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> Hubungkan WhatsApp';

                if (statusData.status === 'qr_ready' || statusData.qr) {
                    const modal = new bootstrap.Modal(document.getElementById('modal-scan-qr'));
                    modal.show();
                }
            }, 1500);
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ki-duotone ki-send fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> Hubungkan WhatsApp';
            Swal.fire('Error', 'Gagal memulai koneksi WhatsApp', 'error');
        }
    });

    // Start polling status when QR modal is opened
    const qrModalEl = document.getElementById('modal-scan-qr');
    qrModalEl.addEventListener('shown.bs.modal', function () {
        checkStatus(true);
        statusPollingTimer = setInterval(() => checkStatus(true), 3500);
    });
    qrModalEl.addEventListener('hidden.bs.modal', function () {
        if (statusPollingTimer) clearInterval(statusPollingTimer);
    });

    // 3. Quick Permohonan Contact Selection
    const selectPermohonan = document.getElementById('select-permohonan-contact');
    selectPermohonan.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            document.getElementById('new-chat-number').value = this.value;
            document.getElementById('new-chat-name').value = selected.getAttribute('data-name') || '';
            const jasa = selected.getAttribute('data-jasa') || '';
            document.getElementById('new-chat-message').value = `Halo kak ${selected.getAttribute('data-name') || ''}, terima kasih sudah mengajukan permohonan ${jasa ? 'jasa ' + jasa : 'project'} di MDT Solution. Kami siap membantu konsultasi Anda!`;
        }
    });

    // 4. Submit New Chat / Add Number
    async function submitNewChat() {
        const numberInput = document.getElementById('new-chat-number');
        const nameInput = document.getElementById('new-chat-name');
        const messageInput = document.getElementById('new-chat-message');

        const numberVal = (numberInput.value || '').trim();
        const nameVal = (nameInput.value || '').trim();
        const messageVal = (messageInput.value || '').trim();

        if (!numberVal) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Perhatian', 'Silakan masukkan nomor WhatsApp tujuan.', 'warning');
            } else {
                alert('Silakan masukkan nomor WhatsApp tujuan.');
            }
            numberInput.focus();
            return;
        }

        if (!messageVal) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Perhatian', 'Silakan ketik pesan awal yang ingin dikirim.', 'warning');
            } else {
                alert('Silakan ketik pesan awal yang ingin dikirim.');
            }
            messageInput.focus();
            return;
        }

        const submitBtn = document.getElementById('btn-submit-new-chat');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';

        try {
            const res = await fetch("{{ route('wa-chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    number: numberVal,
                    name: nameVal,
                    message: messageVal
                })
            });
            const data = await res.json();

            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ki-duotone ki-send fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> Kirim Pesan & Buka Chat';

            if (data.chat) {
                // Safely dismiss modal
                const modalEl = document.getElementById('modal-new-chat');
                try {
                    const modalInst = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (modalInst) modalInst.hide();
                } catch (e) {
                    modalEl.querySelector('[data-bs-dismiss="modal"]')?.click();
                }

                // Select and open chat in the right panel
                selectChat(data.chat.id, data.chat.number, data.chat.name);

                // Reload left chat list
                reloadChatList();

                if (data.success) {
                    if (typeof toastr !== 'undefined') toastr.success('Pesan WhatsApp berhasil dikirim ke ' + data.chat.number);
                } else {
                    if (typeof toastr !== 'undefined') toastr.warning(data.message || 'Pesan tersimpan, namun gateway belum terhubung.');
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat memproses chat.', 'error');
                } else {
                    alert(data.message || 'Terjadi kesalahan saat memproses chat.');
                }
            }
        } catch (err) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ki-duotone ki-send fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> Kirim Pesan & Buka Chat';
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Gagal mengirim pesan: ' + err.message, 'error');
            } else {
                alert('Gagal mengirim pesan: ' + err.message);
            }
        }
    }

    document.getElementById('btn-submit-new-chat').addEventListener('click', submitNewChat);
    document.getElementById('form-new-chat').addEventListener('submit', function (e) {
        e.preventDefault();
        submitNewChat();
    });

    // 5. Select Chat Function
    function selectChat(id, number, name) {
        currentChatId = id;
        currentChatNumber = number;
        currentChatName = name || number;

        // Highlight in list
        document.querySelectorAll('.chat-list-item').forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-chat-id') == id || item.getAttribute('data-number') == number) {
                item.classList.add('active');
            }
        });

        // Switch to active window
        document.getElementById('chat-window-empty').classList.add('d-none');
        document.getElementById('chat-window-active').classList.remove('d-none');
        document.getElementById('chat-window-active').classList.add('d-flex');

        // Update header
        document.getElementById('active-chat-name').textContent = currentChatName;
        document.getElementById('active-chat-number').textContent = currentChatNumber;
        document.getElementById('active-chat-avatar').textContent = currentChatName.substring(0, 2).toUpperCase();
        document.getElementById('active-chat-wa-link').href = `https://wa.me/${currentChatNumber}`;

        document.getElementById('active-chat-target-number').value = currentChatNumber;
        document.getElementById('active-chat-target-name').value = currentChatName;

        // Load messages
        loadMessages(currentChatId || currentChatNumber);
    }

    // Attach click to existing chat list items and delete chat button
    document.getElementById('chat-list-container').addEventListener('click', async function (e) {
        const deleteBtn = e.target.closest('.btn-delete-chat');
        if (deleteBtn) {
            e.stopPropagation();
            const chatId = deleteBtn.getAttribute('data-chat-id');
            const number = deleteBtn.getAttribute('data-number');
            await deleteChatConversation(chatId, number);
            return;
        }

        const item = e.target.closest('.chat-list-item');
        if (item) {
            const id = item.getAttribute('data-chat-id');
            const number = item.getAttribute('data-number');
            const name = item.getAttribute('data-name');
            selectChat(id, number, name);
        }
    });

    // 6. Load Messages Function
    async function loadMessages(chatId) {
        const container = document.getElementById('messages-list');
        container.innerHTML = '<div class="text-center py-8"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="fs-7 text-muted ms-2">Memuat pesan...</span></div>';

        try {
            const res = await fetch(`{{ url('wa-chat/chats') }}/${chatId}`);
            const data = await res.json();

            container.innerHTML = '';
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const bubble = document.createElement('div');
                    bubble.className = `chat-bubble ${msg.direction === 'outgoing' ? 'outgoing' : 'incoming'}`;
                    bubble.setAttribute('data-msg-id', msg.id);
                    
                    let statusIcon = '';
                    if (msg.direction === 'outgoing') {
                        if (msg.status === 'sent') {
                            statusIcon = '<i class="ki-duotone ki-double-check text-primary fs-8 ms-1"><span class="path1"></span><span class="path2"></span></i>';
                        } else if (msg.status === 'failed') {
                            statusIcon = '<i class="ki-duotone ki-information-4 text-danger fs-8 ms-1" title="Gagal terkirim"></i>';
                        } else {
                            statusIcon = '<i class="ki-duotone ki-time text-muted fs-8 ms-1"></i>';
                        }
                    }

                    const deleteBtnHtml = msg.id ? `
                        <button type="button" class="btn btn-icon btn-xs p-0 text-muted btn-delete-msg" data-msg-id="${msg.id}" title="Hapus pesan ini">
                            <i class="ki-duotone ki-trash fs-8"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>
                    ` : '';

                    bubble.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="message-text flex-grow-1">${escapeHtml(msg.message).replace(/\n/g, '<br>')}</div>
                            ${deleteBtnHtml}
                        </div>
                        <div class="d-flex align-items-center justify-content-end gap-1 mt-1 fs-9 text-muted">
                            <span>${msg.time || ''}</span>
                            ${statusIcon}
                        </div>
                    `;
                    container.appendChild(bubble);
                });
            } else {
                container.innerHTML = '<div class="text-center py-10 text-muted fs-7">Belum ada riwayat pesan dalam obrolan ini.<br>Ketik pesan di bawah untuk memulai percakapan.</div>';
            }

            // Scroll to bottom
            const scrollArea = document.getElementById('messages-container');
            scrollArea.scrollTop = scrollArea.scrollHeight;
        } catch (err) {
            container.innerHTML = '<div class="text-center py-4 text-danger fs-7">Gagal memuat pesan.</div>';
        }
    }

    // Event delegation for deleting a message from bubble
    document.getElementById('messages-list').addEventListener('click', async function (e) {
        const deleteMsgBtn = e.target.closest('.btn-delete-msg');
        if (deleteMsgBtn) {
            e.stopPropagation();
            const msgId = deleteMsgBtn.getAttribute('data-msg-id');
            const bubbleEl = deleteMsgBtn.closest('.chat-bubble');
            await deleteSingleMessage(msgId, bubbleEl);
        }
    });

    // Function to delete a single message
    async function deleteSingleMessage(msgId, bubbleEl) {
        if (!msgId) return;

        let confirmed = false;
        if (typeof Swal !== 'undefined') {
            const res = await Swal.fire({
                title: 'Hapus Pesan?',
                text: 'Apakah Anda yakin ingin menghapus pesan ini dari riwayat?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });
            confirmed = res.isConfirmed;
        } else {
            confirmed = confirm('Apakah Anda yakin ingin menghapus pesan ini?');
        }

        if (!confirmed) return;

        try {
            const res = await fetch(`{{ url('wa-chat/messages') }}/${msgId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (data.success) {
                if (bubbleEl) {
                    bubbleEl.style.transition = 'all 0.25s ease';
                    bubbleEl.style.opacity = '0';
                    bubbleEl.style.transform = 'scale(0.85)';
                    setTimeout(() => {
                        bubbleEl.remove();
                        const container = document.getElementById('messages-list');
                        if (container && container.querySelectorAll('.chat-bubble').length === 0) {
                            container.innerHTML = '<div class="text-center py-10 text-muted fs-7">Belum ada riwayat pesan dalam obrolan ini.<br>Ketik pesan di bawah untuk memulai percakapan.</div>';
                        }
                    }, 250);
                }

                if (typeof toastr !== 'undefined') toastr.success('Pesan berhasil dihapus.');
                reloadChatList();
            } else {
                if (typeof toastr !== 'undefined') toastr.error(data.message || 'Gagal menghapus pesan.');
            }
        } catch (err) {
            if (typeof toastr !== 'undefined') toastr.error('Error menghapus pesan: ' + err.message);
        }
    }

    // Function to delete entire chat by ID or Phone number
    async function deleteChatConversation(chatId, chatNumber) {
        const targetNumber = chatNumber || currentChatNumber;
        const targetId = chatId || currentChatId || targetNumber;

        if (!targetNumber && !targetId) return;

        let confirmed = false;
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: 'Hapus Seluruh Obrolan?',
                text: `Apakah Anda yakin ingin menghapus semua pesan dan riwayat obrolan dengan nomor ${targetNumber || ''}? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Obrolan',
                cancelButtonText: 'Batal'
            });
            confirmed = result.isConfirmed;
        } else {
            confirmed = confirm(`Apakah Anda yakin ingin menghapus semua obrolan dengan nomor ${targetNumber}?`);
        }

        if (!confirmed) return;

        try {
            const res = await fetch(`{{ url('wa-chat/chats') }}/${targetId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (data.success) {
                // If active chat was deleted, reset right window to empty
                if (currentChatNumber === targetNumber || currentChatId == targetId) {
                    currentChatId = '';
                    currentChatNumber = '';
                    currentChatName = '';
                    document.getElementById('chat-window-active').classList.add('d-none');
                    document.getElementById('chat-window-active').classList.remove('d-flex');
                    document.getElementById('chat-window-empty').classList.remove('d-none');
                }

                if (typeof toastr !== 'undefined') toastr.success('Obrolan berhasil dihapus.');
                reloadChatList();
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal', data.message || 'Gagal menghapus obrolan.', 'error');
                } else {
                    alert(data.message || 'Gagal menghapus obrolan.');
                }
            }
        } catch (err) {
            if (typeof toastr !== 'undefined') toastr.error('Gagal menghapus obrolan: ' + err.message);
        }
    }

    // Tombol Hapus Obrolan di Header Chat Aktif
    document.getElementById('btn-delete-active-chat').addEventListener('click', function () {
        deleteChatConversation(currentChatId, currentChatNumber);
    });

    document.getElementById('btn-refresh-messages').addEventListener('click', function () {
        if (currentChatNumber) {
            loadMessages(currentChatId || currentChatNumber);
        }
    });

    // 7. Send Active Chat Message
    async function sendActiveChatMessage() {
        const input = document.getElementById('message-input');
        const text = input.value.trim();
        if (!text || !currentChatNumber) return;

        const sendBtn = document.getElementById('btn-send-message');
        sendBtn.disabled = true;

        // Optimistically append outgoing bubble
        const container = document.getElementById('messages-list');
        const tempBubble = document.createElement('div');
        tempBubble.className = 'chat-bubble outgoing';
        tempBubble.innerHTML = `
            <div class="message-text">${escapeHtml(text).replace(/\n/g, '<br>')}</div>
            <div class="d-flex align-items-center justify-content-end gap-1 mt-1 fs-9 text-muted">
                <span>Baru saja</span>
                <span class="spinner-border spinner-border-sm" style="width: 10px; height: 10px;"></span>
            </div>
        `;
        container.appendChild(tempBubble);
        const scrollArea = document.getElementById('messages-container');
        scrollArea.scrollTop = scrollArea.scrollHeight;

        input.value = '';

        try {
            const res = await fetch("{{ route('wa-chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    number: currentChatNumber,
                    name: currentChatName,
                    message: text
                })
            });
            const data = await res.json();
            sendBtn.disabled = false;

            if (data.sent_message) {
                // Update temporary bubble to final state
                let statusIcon = data.sent_message.status === 'sent' 
                    ? '<i class="ki-duotone ki-double-check text-primary fs-8 ms-1"><span class="path1"></span><span class="path2"></span></i>'
                    : '<i class="ki-duotone ki-information-4 text-danger fs-8 ms-1" title="Gagal terkirim"></i>';

                tempBubble.setAttribute('data-msg-id', data.sent_message.id);
                tempBubble.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="message-text flex-grow-1">${escapeHtml(data.sent_message.message).replace(/\n/g, '<br>')}</div>
                        <button type="button" class="btn btn-icon btn-xs p-0 text-muted btn-delete-msg" data-msg-id="${data.sent_message.id}" title="Hapus pesan ini">
                            <i class="ki-duotone ki-trash fs-8"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center justify-content-end gap-1 mt-1 fs-9 text-muted">
                        <span>${data.sent_message.time}</span>
                        ${statusIcon}
                    </div>
                `;

                if (!data.success) {
                    if (typeof toastr !== 'undefined') toastr.warning(data.message || 'Pesan dicatat, tetapi pengiriman gateway gagal.');
                }
            }

            reloadChatList();
        } catch (err) {
            sendBtn.disabled = false;
            if (typeof toastr !== 'undefined') toastr.error('Gagal mengirim pesan: ' + err.message);
        }
    }

    document.getElementById('btn-send-message').addEventListener('click', sendActiveChatMessage);
    document.getElementById('form-send-message').addEventListener('submit', function (e) {
        e.preventDefault();
        sendActiveChatMessage();
    });

    // Enter to send message, Shift+Enter for new line
    document.getElementById('message-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendActiveChatMessage();
        }
    });

    // Quick Reply Buttons
    document.querySelectorAll('.quick-reply-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.getAttribute('data-text');
            const input = document.getElementById('message-input');
            input.value = text;
            input.focus();
        });
    });

    // 8. Search Filter in Chat List
    document.getElementById('chat-search-input').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.chat-list-item').forEach(item => {
            const name = item.querySelector('.contact-name').textContent.toLowerCase();
            const number = item.querySelector('.contact-number').textContent.toLowerCase();
            if (name.includes(query) || number.includes(query)) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    });

    // 9. Reload Chat List
    async function reloadChatList() {
        try {
            const res = await fetch("{{ route('wa-chat.chats') }}");
            const data = await res.json();
            if (data.chats && data.chats.length > 0) {
                const container = document.getElementById('chat-list-container');
                container.innerHTML = '';
                data.chats.forEach(chat => {
                    const item = document.createElement('div');
                    item.className = `chat-list-item p-3 border-bottom cursor-pointer d-flex align-items-center justify-content-between transition-all ${chat.id == currentChatId || chat.number == currentChatNumber ? 'active' : ''}`;
                    item.setAttribute('data-chat-id', chat.id);
                    item.setAttribute('data-number', chat.number);
                    item.setAttribute('data-name', chat.name);

                    item.innerHTML = `
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <div class="wa-avatar flex-shrink-0">
                                ${escapeHtml(chat.name.substring(0, 2).toUpperCase())}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-gray-900 text-truncate contact-name">${escapeHtml(chat.name)}</div>
                                <div class="text-muted fs-8 text-truncate contact-number font-monospace">${escapeHtml(chat.number)}</div>
                                <div class="text-gray-600 fs-7 text-truncate contact-last-message mt-1" style="max-width: 160px;">
                                    ${escapeHtml(chat.last_message || 'Belum ada pesan')}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end justify-content-between flex-shrink-0 ms-2" style="min-height: 40px;">
                            <div class="text-muted fs-8 contact-time">${escapeHtml(chat.last_message_time || '')}</div>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                ${chat.unread_count > 0 ? `<span class="badge badge-circle badge-success">${chat.unread_count}</span>` : ''}
                                <button type="button" class="btn btn-icon btn-xs btn-active-light-danger btn-delete-chat text-muted p-0" data-chat-id="${chat.id}" data-number="${chat.number}" title="Hapus Obrolan">
                                    <i class="ki-duotone ki-trash fs-6"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </div>
                        </div>
                    `;
                    container.appendChild(item);
                });
            }
        } catch (err) {
            console.error('Failed to reload chat list:', err);
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>
@endpush
@endsection
