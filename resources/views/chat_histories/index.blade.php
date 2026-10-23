@extends('layouts.admin')

@section('page_title', 'Manajemen & Riwayat Chat AI')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen &amp; Riwayat Chat AI</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Pantau seluruh percakapan pengunjung website dengan MDT AI Assistant</span>
        </h3>
        <div class="card-toolbar">
            <form action="{{ route('chat-histories.index') }}" method="GET" class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid w-250px ps-12" placeholder="Cari IP / Kata Kunci Pesan..." />
                @if(request('search'))
                    <a href="{{ route('chat-histories.index') }}" class="btn btn-icon btn-sm btn-light ms-2" title="Reset Pencarian">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body py-3">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-success">Berhasil</h4>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="w-50px">No</th>
                        <th class="min-w-180px">Pengunjung / IP</th>
                        <th class="min-w-280px">Cuplikan Pesan Terakhir</th>
                        <th class="min-w-100px text-center">Total Pesan</th>
                        <th class="min-w-160px">Aktivitas Terakhir</th>
                        <th class="min-w-120px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $key => $session)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold fs-6">{{ $sessions->firstItem() + $key }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-primary text-primary fw-bold">
                                        <i class="ki-duotone ki-user fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-900 fw-bold fs-6">{{ $session->ip_address ?? 'IP Tidak Terdeteksi' }}</span>
                                    <span class="text-muted fw-semibold fs-8" title="{{ $session->user_agent }}">
                                        {{ Str::limit($session->user_agent ?? 'Browser Pengunjung', 40) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $lastMessage = $session->messages->first();
                            @endphp
                            @if($lastMessage)
                                <div class="d-flex align-items-start">
                                    <span class="badge {{ $lastMessage->role === 'user' ? 'badge-light-primary' : 'badge-light-info' }} me-2 fs-9">
                                        {{ $lastMessage->role === 'user' ? 'User' : 'AI' }}
                                    </span>
                                    <span class="text-gray-700 fw-semibold fs-7" title="{{ $lastMessage->content }}">
                                        {{ Str::limit($lastMessage->content, 90) }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted fs-7 italic">Belum ada pertukaran pesan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-primary fs-7 fw-bold px-3 py-2">
                                <i class="ki-duotone ki-messages fs-6 text-primary me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                {{ $session->messages_count }} Pesan
                            </span>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-semibold d-block fs-7">
                                {{ $session->last_activity_at ? $session->last_activity_at->diffForHumans() : '-' }}
                            </span>
                            <span class="text-muted fs-8">
                                {{ $session->last_activity_at ? $session->last_activity_at->format('d/m/Y H:i') : '' }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" 
                                    class="btn btn-sm btn-light-primary me-2" 
                                    onclick="viewChatTranscript({{ $session->id }})"
                                    title="Lihat Riwayat Percakapan">
                                <i class="ki-duotone ki-eye fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                Detail Chat
                            </button>
                            <button type="button" 
                                    class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#kt_modal_delete_chat_{{ $session->id }}"
                                    title="Hapus Sesi">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="kt_modal_delete_chat_{{ $session->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-450px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Hapus Riwayat Chat</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body text-center py-8">
                                    <i class="ki-duotone ki-information-5 fs-5tx text-danger mb-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    <p class="fs-6 text-gray-700">Apakah Anda yakin ingin menghapus seluruh riwayat chat dari IP <strong class="text-dark">{{ $session->ip_address }}</strong> ({{ $session->messages_count }} pesan)?</p>
                                    <p class="fs-8 text-muted">Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <form action="{{ route('chat-histories.destroy', $session->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10">
                            <i class="ki-duotone ki-message-text-2 fs-3tx text-muted mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <div class="text-gray-500 fs-6 fw-semibold">Belum ada riwayat percakapan chatbot.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <div class="fs-7 text-muted">
                Menampilkan {{ $sessions->firstItem() ?? 0 }} sampai {{ $sessions->lastItem() ?? 0 }} dari {{ $sessions->total() }} sesi chat
            </div>
            <div>
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transkrip Percakapan -->
<div class="modal fade" id="kt_modal_chat_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light py-4 px-6 border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px me-3">
                        <span class="symbol-label bg-primary text-white">
                            <i class="ki-duotone ki-technology-4 fs-2x text-white"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                    <div>
                        <h3 class="fw-bold text-gray-900 mb-0" id="chat-modal-title">Transkrip Percakapan AI</h3>
                        <div class="text-muted fs-8 mt-1" id="chat-modal-meta">Memuat detail sesi...</div>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body p-6 bg-light-subtle" style="max-height: 550px; overflow-y: auto;" id="chat-modal-body">
                <div class="text-center py-10" id="chat-modal-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted fs-7 mt-2">Mengambil riwayat pesan...</div>
                </div>
                <div id="chat-transcript-container" class="d-flex flex-column gap-3 d-none">
                    <!-- Chat bubbles injected via Javascript -->
                </div>
            </div>

            <div class="modal-footer bg-light py-3 px-6 border-0 d-flex justify-content-between align-items-center">
                <span class="text-muted fs-8" id="chat-modal-footer-count">0 Pesan Tercatat</span>
                <button type="button" class="btn btn-sm btn-light-primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Load Marked.js if needed for formatting AI answers in modal -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function viewChatTranscript(sessionId) {
    const modalEl = document.getElementById('kt_modal_chat_detail');
    const modal = new bootstrap.Modal(modalEl);
    
    const loadingEl = document.getElementById('chat-modal-loading');
    const containerEl = document.getElementById('chat-transcript-container');
    const metaEl = document.getElementById('chat-modal-meta');
    const countEl = document.getElementById('chat-modal-footer-count');
    
    loadingEl.classList.remove('d-none');
    containerEl.classList.add('d-none');
    containerEl.innerHTML = '';
    metaEl.innerText = 'Mengambil detail sesi...';
    
    modal.show();
    
    fetch(`/chat-histories/${sessionId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        loadingEl.classList.add('d-none');
        containerEl.classList.remove('d-none');
        
        if (data.success) {
            metaEl.innerHTML = `IP: <strong>${data.session.ip_address || '-'}</strong> | Dimulai: <strong>${data.session.created_at || '-'}</strong>`;
            countEl.innerText = `Total: ${data.session.messages_count} Pesan`;
            
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const isUser = msg.role === 'user';
                    
                    let parsedContent = '';
                    if (!isUser && typeof marked !== 'undefined' && marked.parse) {
                        parsedContent = marked.parse(msg.content);
                    } else {
                        parsedContent = escapeHtml(msg.content).replace(/\n/g, '<br>');
                    }
                    
                    const bubble = document.createElement('div');
                    bubble.className = `d-flex flex-column ${isUser ? 'align-items-end' : 'align-items-start'} mb-2`;
                    
                    bubble.innerHTML = `
                        <div class="d-flex align-items-center mb-1">
                            <span class="fs-8 fw-bold ${isUser ? 'text-primary me-2' : 'text-gray-700 me-2'}">
                                ${isUser ? '👤 Pengunjung' : '🤖 MDT Assistant'}
                            </span>
                            <span class="text-muted fs-9">${msg.created_at || ''}</span>
                        </div>
                        <div class="p-4 rounded-3 shadow-sm" style="max-width: 82%; font-size: 13.5px; line-height: 1.6; ${
                            isUser 
                            ? 'background: #3b82f6; color: #ffffff; border-top-right-radius: 2px !important;' 
                            : 'background: #ffffff; color: #1e293b; border: 1px solid #e2e8f0; border-top-left-radius: 2px !important;'
                        }">
                            <div class="chat-bubble-text">${parsedContent}</div>
                        </div>
                    `;
                    containerEl.appendChild(bubble);
                });
            } else {
                containerEl.innerHTML = '<div class="text-center text-muted py-8">Tidak ada riwayat pesan pada sesi ini.</div>';
            }
        } else {
            containerEl.innerHTML = '<div class="alert alert-danger">Gagal memuat riwayat pesan.</div>';
        }
    })
    .catch(err => {
        console.error(err);
        loadingEl.classList.add('d-none');
        containerEl.classList.remove('d-none');
        containerEl.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data riwayat percakapan.</div>';
    });
}
</script>
@endsection
