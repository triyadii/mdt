@extends('layouts.admin')

@section('page_title', 'Manajemen Jasa & Harga Project')
@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Jasa &amp; Harga Project</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola daftar layanan, paket harga mulai, dan estimasi pengerjaan</span>
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_jasa">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Jasa &amp; Harga
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

        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="w-50px">No</th>
                        <th class="min-w-150px">Nama Jasa</th>
                        <th class="min-w-150px">Estimasi Harga Mulai</th>
                        <th class="min-w-150px">Estimasi Waktu</th>
                        <th class="min-w-200px">Keterangan</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jasas as $key => $jasa)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold fs-6">{{ $key + 1 }}</span>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-bold d-block fs-6">{{ $jasa->namaJasa }}</span>
                        </td>
                        <td>
                            <span class="badge badge-light-success fs-7 fw-bold">{{ $jasa->harga_mulai ?? 'Belum Diatur' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-light-info fs-7 fw-semibold">{{ $jasa->estimasi_waktu ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-muted fw-semibold d-block fs-7">{{ Str::limit($jasa->keteranganJasa, 50) }}</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_jasa_{{ $jasa->uuid }}"><i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i></button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_jasa_{{ $jasa->uuid }}"><i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span></i></button>
                        </td>
                    </tr>

<!-- Edit Modal -->
<div class="modal fade text-start" id="kt_modal_edit_jasa_{{ $jasa->uuid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Edit Jasa &amp; Harga Project</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('jasas.update', $jasa->uuid) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Jasa</span></label>
                        <input type="text" class="form-control form-control-solid" name="namaJasa" value="{{ $jasa->namaJasa }}" required />
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Estimasi Harga Mulai</label>
                            <input type="text" class="form-control form-control-solid" name="harga_mulai" placeholder="Contoh: Rp 3.500.000 atau Gratis" value="{{ $jasa->harga_mulai }}" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Estimasi Waktu</label>
                            <input type="text" class="form-control form-control-solid" name="estimasi_waktu" placeholder="Contoh: 1 - 3 Minggu" value="{{ $jasa->estimasi_waktu }}" />
                        </div>
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Keterangan Jasa</label>
                        <textarea class="form-control form-control-solid" name="keteranganJasa" rows="4">{{ $jasa->keteranganJasa }}</textarea>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="kt_modal_delete_jasa_{{ $jasa->uuid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Hapus Jasa</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="text-center mb-13">
                    <div class="text-muted fw-semibold fs-5">
                        Apakah Anda yakin ingin menghapus jasa <span class="text-danger fw-bold">{{ $jasa->namaJasa }}</span>?
                    </div>
                </div>
                <div class="text-center">
                    <form action="{{ route('jasas.destroy', $jasa->uuid) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-end mt-5">
                {{ $jasas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade text-start" id="kt_modal_add_jasa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Jasa Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('jasas.store') }}" method="POST" class="form">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Jasa</span></label>
                        <input type="text" class="form-control form-control-solid" name="namaJasa" placeholder="Masukkan nama jasa" required />
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Estimasi Harga Mulai</label>
                            <input type="text" class="form-control form-control-solid" name="harga_mulai" placeholder="Contoh: Rp 3.500.000 atau Gratis" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Estimasi Waktu</label>
                            <input type="text" class="form-control form-control-solid" name="estimasi_waktu" placeholder="Contoh: 1 - 3 Minggu" />
                        </div>
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Keterangan Jasa</label>
                        <textarea class="form-control form-control-solid" name="keteranganJasa" rows="4" placeholder="Masukkan keterangan jasa"></textarea>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
