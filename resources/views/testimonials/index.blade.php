@extends('layouts.admin')

@section('page_title', 'Manajemen Testimoni')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Testimoni</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola daftar testimoni dari klien/customer</span>
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_testimonial">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Testimoni
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
                        <th class="min-w-200px">Nama Klien / Instansi</th>
                        <th class="min-w-300px">Keterangan Testimoni</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testimonials as $key => $testimonial)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold fs-6">{{ $key + 1 }}</span>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-bold d-block fs-6">{{ $testimonial->nama_testimoni }}</span>
                        </td>
                        <td>
                            <div class="text-gray-700 fs-7" style="max-height: 80px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                {{ $testimonial->keterangan_testimoni }}
                            </div>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_testimonial_{{ $testimonial->uuid }}">
                                <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_testimonial_{{ $testimonial->uuid }}">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade text-start" id="kt_modal_edit_testimonial_{{ $testimonial->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Edit Testimoni</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <form action="{{ route('testimonials.update', $testimonial->uuid) }}" method="POST" class="form">
                                        @csrf
                                        @method('PUT')
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Klien / Instansi</span></label>
                                            <input type="text" class="form-control form-control-solid" name="nama_testimoni" value="{{ $testimonial->nama_testimoni }}" required />
                                        </div>
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Keterangan Testimoni</span></label>
                                            <textarea class="form-control form-control-solid" name="keterangan_testimoni" rows="5" required>{{ $testimonial->keterangan_testimoni }}</textarea>
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
                    <div class="modal fade text-start" id="kt_modal_delete_testimonial_{{ $testimonial->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-400px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Hapus Testimoni</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-danger" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body text-center py-10">
                                    <div class="mb-5">
                                        <i class="ki-duotone ki-warning fs-5x text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </div>
                                    <h4>Apakah Anda yakin ingin menghapus testimoni dari <strong>{{ $testimonial->nama_testimoni }}</strong>?</h4>
                                    <div class="mt-10">
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('testimonials.destroy', $testimonial->uuid) }}" method="POST" style="display:inline-block;">
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
                        <td colspan="4" class="text-center text-muted">Belum ada data testimoni.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Testimonial -->
<div class="modal fade" id="kt_modal_add_testimonial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Testimoni</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('testimonials.store') }}" method="POST" class="form">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Klien / Instansi</span></label>
                        <input type="text" class="form-control form-control-solid" name="nama_testimoni" required />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Keterangan Testimoni</span></label>
                        <textarea class="form-control form-control-solid" name="keterangan_testimoni" rows="5" required></textarea>
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
