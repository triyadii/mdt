@extends('layouts.admin')

@section('page_title', 'Buat Kampanye WhatsApp Blast')

@section('content')
<div class="d-flex flex-column gap-5">
    <!--begin::Breadcrumb / Header Nav-->
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="fw-bold text-gray-900 mb-1">Buat Kampanye Promosi Baru</h3>
            <div class="text-muted fs-7">Rancang pesan broadcast promosi dan tentukan daftar nomor penerima target.</div>
        </div>
        <a href="{{ route('wa-blast.index') }}" class="btn btn-sm btn-light">
            <i class="ki-duotone ki-arrow-left fs-4 me-1"></i> Kembali ke Daftar
        </a>
    </div>
    <!--end::Header Nav-->

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center p-4">
            <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            <div class="d-flex flex-column">
                <span class="fw-bold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <form action="{{ route('wa-blast.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-7">
            <!--Left: Form Configuration-->
            <div class="col-xl-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-0 pt-6">
                        <h4 class="card-title fw-bold text-gray-800">
                            <i class="ki-duotone ki-message-text-2 text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Detail Kampanye & Template Pesan
                        </h4>
                    </div>
                    <div class="card-body py-4">
                        <!--Title-->
                        <div class="mb-5">
                            <label class="form-label required fw-bold text-gray-700">Nama Kampanye / Judul Promosi:</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Promo Akhir Tahun Pembuatan Website MDT" value="{{ old('title') }}" required />
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-8 mt-1">Judul internal untuk memudahkan Anda mengidentifikasi riwayat kampanye ini.</div>
                        </div>

                        <!--Message Template-->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label required fw-bold text-gray-700 m-0">Konten Pesan Promosi:</label>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-muted fs-8 me-1">Sisipkan Tag:</span>
                                    <button type="button" class="btn btn-xs btn-light-primary py-1 px-2 fs-9 insert-tag-btn" data-tag="{nama}">+ {nama}</button>
                                    <button type="button" class="btn btn-xs btn-light-info py-1 px-2 fs-9 insert-tag-btn" data-tag="{nomor}">+ {nomor}</button>
                                    <button type="button" class="btn btn-xs btn-light-success py-1 px-2 fs-9 insert-tag-btn" data-tag="{tanggal}">+ {tanggal}</button>
                                </div>
                            </div>
                            <textarea name="message" id="blast-message-textarea" class="form-control @error('message') is-invalid @enderror" rows="8" placeholder="Tuliskan pesan promosi Anda di sini..." required>{{ old('message', "Halo kak {nama},\n\nKabar gembira dari MDT Solution Indonesia! 🚀\nSaat ini kami sedang menghadirkan promo spesial diskon hingga 25% untuk pembuatan Sistem Informasi, Aplikasi Web & Mobile, serta Company Profile profesional.\n\nKonsultasikan kebutuhan digital bisnis Anda secara GRATIS sekarang dengan membalas pesan ini atau kunjungi website kami di: https://mdt-solution.site\n\nTerima kasih dan salam sukses!\nTim MDT Solution") }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between text-muted fs-8 mt-1">
                                <span>Gunakan tag <code>{nama}</code> untuk menyapa nama pelanggan secara otomatis.</span>
                                <span id="char-counter">0 karakter</span>
                            </div>
                        </div>

                        <div class="separator my-6"></div>

                        <!--Delay Setting-->
                        <div class="mb-4">
                            <label class="form-label required fw-bold text-gray-700">Jeda Antar Pesan (Delay Detik):</label>
                            <div class="input-group" style="max-width: 250px;">
                                <input type="number" name="delay_seconds" class="form-control" min="1" max="60" value="{{ old('delay_seconds', 3) }}" required />
                                <span class="input-group-text">Detik / Pesan</span>
                            </div>
                            <div class="text-muted fs-8 mt-2">
                                <i class="ki-duotone ki-information-2 text-warning fs-6 me-1"></i>
                                Rekomendasi: <strong>3 - 5 detik</strong> per pesan agar pengiriman aman dan tidak terdeteksi spam oleh WhatsApp.
                            </div>
                        </div>
                    </div>
                </div>

                <!--Recipient Sources Card-->
                <div class="card shadow-sm border-0 mt-5">
                    <div class="card-header border-0 pt-6">
                        <h4 class="card-title fw-bold text-gray-800">
                            <i class="ki-duotone ki-people text-success fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            Target Nomor Penerima
                        </h4>
                    </div>
                    <div class="card-body py-4">
                        <!--Option 1: Checkbox Permohonan-->
                        <div class="bg-light-primary rounded border border-primary border-dashed p-4 mb-5">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="import_permohonan" value="1" id="check-import-permohonan" {{ old('import_permohonan') ? 'checked' : '' }} />
                                <label class="form-check-label ms-3" for="check-import-permohonan">
                                    <div class="fw-bold text-gray-900 fs-6">Sertakan Kontak dari Data Permohonan Klien MDT</div>
                                    <div class="text-muted fs-7">
                                        Otomatis menarik <span class="badge badge-primary">{{ $totalPermohonan }} kontak</span> calon klien yang pernah mengajukan permohonan layanan di website.
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!--Option 2: Manual Textarea-->
                        <div class="mb-5">
                            <label class="form-label fw-bold text-gray-700">Masukkan Nomor Manual (1 baris per penerima):</label>
                            <textarea name="recipients_manual" id="recipients-manual" class="form-control font-monospace fs-7" rows="6" placeholder="Format: 08123456789 atau Nama, 08123456789&#10;Contoh:&#10;Budi Santoso, 081234567890&#10;08987654321&#10;PT Sukses Abadi, 6281122334455">{{ old('recipients_manual') }}</textarea>
                            <div class="d-flex justify-content-between text-muted fs-8 mt-1">
                                <span>Bisa memasukkan langsung nomor (08... atau 628...) atau format <code>Nama, Nomor</code>.</span>
                                <span id="manual-numbers-counter">0 nomor terdeteksi</span>
                            </div>
                        </div>

                        <!--Option 3: File Upload-->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-gray-700">Atau Impor dari File (.CSV / .TXT):</label>
                            <input type="file" name="file_recipients" class="form-control" accept=".csv,.txt" />
                            <div class="text-muted fs-8 mt-1">File teks berisi daftar nomor per baris atau CSV dengan kolom Nama, Nomor.</div>
                        </div>
                    </div>
                </div>

                <!--Submit Button-->
                <div class="d-flex align-items-center justify-content-end gap-3 mt-5">
                    <a href="{{ route('wa-blast.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-success fw-bold px-6 py-3">
                        <i class="ki-duotone ki-check fs-2 me-1"></i> Simpan & Lanjutkan ke Pengiriman
                    </button>
                </div>
            </div>

            <!--Right: Live WhatsApp Preview & Tips-->
            <div class="col-xl-5">
                <!--WhatsApp Message Mockup-->
                <div class="card shadow-sm border-0 sticky-top" style="top: 25px;">
                    <div class="card-header border-0 bg-success text-white py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-35px symbol-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold">
                                MDT
                            </div>
                            <div>
                                <div class="fw-bold text-white fs-6">MDT Solution Official</div>
                                <div class="fs-8 text-white-50">Pratinjau Tampilan Pesan WhatsApp</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" style="background-color: #efeae2; background-image: radial-gradient(#d1d7db 1px, transparent 1px); background-size: 20px 20px; min-height: 380px;">
                        <div class="d-flex flex-column gap-2">
                            <div class="p-3 rounded-3 shadow-sm" style="background-color: #d9fdd3; color: #111b21; max-width: 90%; align-self: flex-end; border-bottom-right-radius: 2px;">
                                <div id="preview-message-body" style="white-space: pre-wrap; font-size: 0.925rem; line-height: 1.45;"></div>
                                <div class="text-end text-muted fs-9 mt-2 d-flex align-items-center justify-content-end gap-1">
                                    <span>Sekarang</span>
                                    <i class="ki-duotone ki-double-check text-primary fs-8">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light-subtle py-4">
                        <div class="d-flex align-items-start gap-3">
                            <i class="ki-duotone ki-shield-tick text-success fs-2x mt-1"><span class="path1"></span><span class="path2"></span></i>
                            <div>
                                <h6 class="fw-bold text-gray-900 mb-1">Tips Promosi WhatsApp Efektif:</h6>
                                <ul class="text-muted fs-8 mb-0 ps-3">
                                    <li>Sapa pelanggan dengan nama agar terasa personal.</li>
                                    <li>Berikan Call-to-Action (CTA) jelas seperti link website atau ajakan membalas pesan.</li>
                                    <li>Hindari mengirim ribuan pesan sekaligus dalam satu detik; gunakan jeda waktu 3-5 detik.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('blast-message-textarea');
    const previewBody = document.getElementById('preview-message-body');
    const charCounter = document.getElementById('char-counter');
    const manualInput = document.getElementById('recipients-manual');
    const manualCounter = document.getElementById('manual-numbers-counter');

    function updatePreview() {
        const text = textarea.value || '';
        charCounter.textContent = text.length + ' karakter';

        // Replace tags with sample preview data
        const sampleText = text
            .replace(/{nama}/g, 'Budi Santoso')
            .replace(/{nomor}/g, '081234567890')
            .replace(/{tanggal}/g, new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));

        previewBody.textContent = sampleText || '(Pesan kosong...)';
    }

    textarea.addEventListener('input', updatePreview);
    updatePreview();

    // Insert Tag Buttons
    document.querySelectorAll('.insert-tag-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const tag = this.getAttribute('data-tag');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;

            textarea.value = text.substring(0, start) + tag + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + tag.length;
            textarea.focus();
            updatePreview();
        });
    });

    // Count manual numbers
    function countManualNumbers() {
        const lines = (manualInput.value || '').split(/\r\n|\r|\n/);
        let count = 0;
        lines.forEach(l => {
            if (l.trim().length > 6) count++;
        });
        manualCounter.textContent = count + ' nomor terdeteksi';
    }

    manualInput.addEventListener('input', countManualNumbers);
    countManualNumbers();
});
</script>
@endpush
@endsection
