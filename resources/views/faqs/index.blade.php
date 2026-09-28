@extends('layouts.admin')

@section('page_title', 'Manajemen FAQ')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen FAQ</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola daftar Frequently Asked Questions (FAQ)</span>
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_faq">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah FAQ
            </button>
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

        @if($errors->any())
            <div class="alert alert-danger p-5 mb-10">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="w-50px">No</th>
                        <th class="min-w-250px">Pertanyaan</th>
                        <th class="min-w-300px">Jawaban</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $key => $faq)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold fs-6">{{ $key + 1 }}</span>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-bold d-block fs-6">{{ $faq->pertanyaan }}</span>
                        </td>
                        <td>
                            <div class="text-gray-700 fs-7" style="max-height: 80px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                {{ $faq->jawaban }}
                            </div>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_faq_{{ $faq->uuid }}" title="Edit">
                                <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_faq_{{ $faq->uuid }}" title="Hapus">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade text-start" id="kt_modal_edit_faq_{{ $faq->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Edit FAQ</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <form action="{{ route('faqs.update', $faq->uuid) }}" method="POST" class="form">
                                        @csrf
                                        @method('PUT')
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Pertanyaan</span></label>
                                            <input type="text" class="form-control form-control-solid" name="pertanyaan" value="{{ $faq->pertanyaan }}" required />
                                        </div>
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Jawaban</span></label>
                                            <textarea class="form-control form-control-solid" name="jawaban" rows="5" required>{{ $faq->jawaban }}</textarea>
                                        </div>
                                        <div class="text-end">
                                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade text-start" id="kt_modal_delete_faq_{{ $faq->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-400px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Hapus FAQ</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-danger" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body text-center py-10">
                                    <div class="mb-5">
                                        <i class="ki-duotone ki-warning fs-5x text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </div>
                                    <h4>Apakah Anda yakin ingin menghapus FAQ ini?</h4>
                                    <p class="text-muted mt-3">Tindakan ini tidak dapat dibatalkan.</p>
                                    <div class="mt-10">
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('faqs.destroy', $faq->uuid) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada data FAQ.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add FAQ -->
<div class="modal fade" id="kt_modal_add_faq" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah FAQ</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('faqs.store') }}" method="POST" class="form">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Pertanyaan</span></label>
                        <input type="text" class="form-control form-control-solid" name="pertanyaan" placeholder="Cth: Bagaimana cara memesan project?" required />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Jawaban</span></label>
                        <textarea class="form-control form-control-solid" name="jawaban" rows="5" placeholder="Tuliskan jawaban yang sesuai..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
