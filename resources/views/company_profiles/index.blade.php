@extends('layouts.admin')

@section('page_title', 'Manajemen Profil')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Profil Perusahaan</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola informasi profil perusahaan Anda</span>
        </h3>
        <div class="card-toolbar">
            @if(!$profile)
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_profile">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Profil
            </button>
            @else
            <button type="button" class="btn btn-sm btn-light-warning" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_profile">
                <i class="ki-duotone ki-pencil fs-2"></i> Edit Profil
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
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">Terjadi Kesalahan</h4>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <tbody>
                    <tr>
                        <td class="w-200px fw-bold text-gray-900">Nama Profil</td>
                        <td class="text-gray-700">{{ $profile ? $profile->nama_profil : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="w-200px fw-bold text-gray-900">Email</td>
                        <td class="text-gray-700">{{ $profile ? $profile->email : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="w-200px fw-bold text-gray-900">Nomor Telepon</td>
                        <td class="text-gray-700">{{ $profile ? $profile->nomor_telepon : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="w-200px fw-bold text-gray-900">Instagram</td>
                        <td class="text-gray-700">{{ $profile && $profile->instagram ? $profile->instagram : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="w-200px fw-bold text-gray-900">TikTok</td>
                        <td class="text-gray-700">{{ $profile && $profile->tiktok ? $profile->tiktok : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="w-200px fw-bold text-gray-900">Thread</td>
                        <td class="text-gray-700">{{ $profile && $profile->thread ? $profile->thread : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="w-200px fw-bold text-gray-900">Alamat</td>
                        <td class="text-gray-700">{{ $profile ? $profile->alamat : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(!$profile)
<!-- Modal Add Profile -->
<div class="modal fade" id="kt_modal_add_profile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Profil Perusahaan</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('company-profiles.store') }}" method="POST" class="form">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Profil</span></label>
                        <input type="text" class="form-control form-control-solid" name="nama_profil" required />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Email</label>
                        <input type="email" class="form-control form-control-solid" name="email" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Nomor Telepon</label>
                        <input type="text" class="form-control form-control-solid" name="nomor_telepon" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Instagram (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" name="instagram" placeholder="Cth: @mdtsolution" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">TikTok (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" name="tiktok" placeholder="Cth: @mdtsolution" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Thread (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" name="thread" placeholder="Cth: @mdtsolution" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Alamat</label>
                        <textarea class="form-control form-control-solid" name="alamat" rows="3"></textarea>
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

@if($profile)
<!-- Modal Edit Profile -->
<div class="modal fade" id="kt_modal_edit_profile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Edit Profil Perusahaan</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('company-profiles.update', $profile->uuid) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Profil</span></label>
                        <input type="text" class="form-control form-control-solid" name="nama_profil" value="{{ $profile->nama_profil }}" required />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Email</label>
                        <input type="email" class="form-control form-control-solid" name="email" value="{{ $profile->email }}" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Nomor Telepon</label>
                        <input type="text" class="form-control form-control-solid" name="nomor_telepon" value="{{ $profile->nomor_telepon }}" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Instagram (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" name="instagram" value="{{ $profile->instagram }}" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">TikTok (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" name="tiktok" value="{{ $profile->tiktok }}" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Thread (Opsional)</label>
                        <input type="text" class="form-control form-control-solid" name="thread" value="{{ $profile->thread }}" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Alamat</label>
                        <textarea class="form-control form-control-solid" name="alamat" rows="3">{{ $profile->alamat }}</textarea>
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
