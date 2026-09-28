@extends('layouts.admin')

@section('page_title', 'Manajemen Tentang Kami')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Tentang (About)</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola deskripsi singkat mengenai MDT Solution</span>
        </h3>
        <div class="card-toolbar">
            @if(!$about)
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_about">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Keterangan
            </button>
            @else
            <button type="button" class="btn btn-sm btn-light-warning" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_about">
                <i class="ki-duotone ki-pencil fs-2"></i> Edit Keterangan
            </button>
            @endif
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

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">Gagal</h4>
                    <span>{{ session('error') }}</span>
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

        <div class="border border-dashed border-gray-300 rounded p-7 mt-5">
            <h4 class="fw-bold text-gray-800 mb-4">Keterangan Tentang Kami</h4>
            @if($about)
                <div class="text-gray-600 fs-5 lh-lg" style="white-space: pre-wrap; text-align: justify;">{{ $about->keterangan_tentang }}</div>
            @else
                <div class="text-muted fs-6">Belum ada keterangan yang ditambahkan. Silakan klik tombol "Tambah Keterangan" di atas.</div>
            @endif
        </div>
    </div>
</div>

@if(!$about)
<!-- Modal Add About -->
<div class="modal fade" id="kt_modal_add_about" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Keterangan Tentang Kami</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('abouts.store') }}" method="POST" class="form">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Keterangan Tentang Kami</span></label>
                        <textarea class="form-control form-control-solid" name="keterangan_tentang" rows="10" placeholder="Tulis deskripsi perusahaan..." required></textarea>
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
@endif

@if($about)
<!-- Modal Edit About -->
<div class="modal fade" id="kt_modal_edit_about" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Edit Keterangan Tentang Kami</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('abouts.update', $about->uuid) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Keterangan Tentang Kami</span></label>
                        <textarea class="form-control form-control-solid" name="keterangan_tentang" rows="10" required>{{ $about->keterangan_tentang }}</textarea>
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
@endif
@endsection
