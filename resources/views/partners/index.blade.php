@extends('layouts.admin')

@section('page_title', 'Manajemen Klien & Mitra')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Perusahaan / Usaha</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola daftar perusahaan yang bekerja sama dengan MDT Solution</span>
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_partner">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Mitra
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
                        <th class="min-w-200px">Nama Perusahaan / Usaha</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partners as $key => $partner)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold fs-6">{{ $key + 1 }}</span>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-bold d-block fs-6">{{ $partner->nama_perusahaan }}</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_partner_{{ $partner->uuid }}" title="Edit">
                                <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_partner_{{ $partner->uuid }}" title="Hapus">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade text-start" id="kt_modal_edit_partner_{{ $partner->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Edit Mitra</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <form action="{{ route('partners.update', $partner->uuid) }}" method="POST" class="form">
                                        @csrf
                                        @method('PUT')
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Perusahaan / Usaha</span></label>
                                            <input type="text" class="form-control form-control-solid" name="nama_perusahaan" value="{{ $partner->nama_perusahaan }}" required />
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
                    <div class="modal fade text-start" id="kt_modal_delete_partner_{{ $partner->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-400px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Hapus Mitra</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-danger" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body text-center py-10">
                                    <div class="mb-5">
                                        <i class="ki-duotone ki-warning fs-5x text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </div>
                                    <h4>Apakah Anda yakin ingin menghapus <strong>{{ $partner->nama_perusahaan }}</strong>?</h4>
                                    <div class="mt-10">
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('partners.destroy', $partner->uuid) }}" method="POST" style="display:inline-block;">
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
                        <td colspan="3" class="text-center text-muted">Belum ada data perusahaan/usaha.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Partner -->
<div class="modal fade" id="kt_modal_add_partner" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Mitra</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('partners.store') }}" method="POST" class="form">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Perusahaan / Usaha</span></label>
                        <input type="text" class="form-control form-control-solid" name="nama_perusahaan" placeholder="Cth: PT Digital Solusi" required />
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
