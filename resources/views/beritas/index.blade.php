@extends('layouts.admin')

@section('page_title', 'Manajemen Berita')
@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Berita</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Daftar semua berita / artikel</span>
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_berita">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Berita
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
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">Terjadi Kesalahan</h4>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="w-50px">No</th>
                        <th class="min-w-150px">Gambar</th>
                        <th class="min-w-200px">Judul Berita</th>
                        <th class="min-w-150px">Author</th>
                        <th class="min-w-150px">Slug</th>
                        <th class="min-w-200px">Keterangan</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($beritas as $key => $berita)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold fs-6">{{ $key + 1 }}</span>
                        </td>
                        <td>
                            @if(!empty($berita->gambar) && is_array($berita->gambar))
                                <div class="d-flex align-items-center">
                                    @foreach($berita->gambar as $gbr)
                                        <div class="symbol symbol-50px me-2">
                                            <img src="{{ asset($gbr) }}" alt="Gambar Berita" style="object-fit: cover;"/>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge badge-light-danger">No Image</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-gray-900 fw-bold d-block fs-6">{{ $berita->namaBerita }}</span>
                        </td>
                        <td>
                            <span class="badge badge-light-primary fs-7 fw-bold">{{ $berita->author ?? 'Admin' }}</span>
                        </td>
                        <td>
                            <span class="text-muted fw-semibold d-block fs-7">{{ $berita->slugBerita }}</span>
                        </td>
                        <td>
                            <span class="text-muted fw-semibold d-block fs-7">{{ Str::limit($berita->keteranganBerita, 50) }}</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_detail_berita_{{ $berita->uuid }}"><i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_berita_{{ $berita->uuid }}"><i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i></button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_berita_{{ $berita->uuid }}"><i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>
                        </td>
                    </tr>

<!-- Detail Modal -->
<div class="modal fade" id="kt_modal_detail_berita_{{ $berita->uuid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-700px">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="fw-bold fs-3">Detail Berita</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-10 pt-0 pb-10">
                <!-- Header Info -->
                <div class="mb-5 mt-3">
                    <h1 class="text-gray-900 fs-2 fw-bold mb-3">{{ $berita->namaBerita }}</h1>
                    <div class="d-flex align-items-center flex-wrap fw-semibold fs-7 text-muted mb-4">
                        <div class="d-flex align-items-center me-5 mb-2">
                            <i class="ki-duotone ki-profile-circle fs-5 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            {{ $berita->author ?? 'Admin' }}
                        </div>
                        <div class="d-flex align-items-center me-5 mb-2">
                            <i class="ki-duotone ki-calendar-8 fs-5 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                            {{ $berita->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
                
                <!-- Separator -->
                <div class="separator separator-dashed mb-5"></div>

                <!-- Images Gallery (Compact) -->
                @if(!empty($berita->gambar) && is_array($berita->gambar))
                    <div class="mb-6">
                        <div class="row g-4">
                            @foreach($berita->gambar as $gbr)
                                <div class="col-4">
                                    <a class="d-block overlay rounded" data-fslightbox="lightbox-modal-{{ $berita->uuid }}" href="{{ asset($gbr) }}">
                                        <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover rounded h-100px h-md-125px"
                                            style="background-image:url('{{ asset($gbr) }}')">
                                        </div>
                                        <div class="overlay-layer rounded bg-dark bg-opacity-25 shadow">
                                            <i class="ki-duotone ki-eye fs-2x text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Content text -->
                <div class="fs-6 fw-normal text-gray-700 lh-lg bg-light rounded p-5" style="text-align: justify; white-space: pre-line;">{{ trim($berita->keteranganBerita) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade text-start" id="kt_modal_edit_berita_{{ $berita->uuid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Edit Berita</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('beritas.update', $berita->uuid) }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf
                    @method('PUT')
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Judul Berita</span></label>
                        <input type="text" class="form-control form-control-solid" name="namaBerita" value="{{ $berita->namaBerita }}" required />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Penulis (Author)</label>
                        <input type="text" class="form-control form-control-solid" name="author" value="{{ $berita->author }}" placeholder="Opsional (Otomatis jika kosong)" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Keterangan Berita</label>
                        <textarea class="form-control form-control-solid" name="keteranganBerita" rows="6">{{ $berita->keteranganBerita }}</textarea>
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Gambar (Maksimal 3)</label>
                        <input type="file" class="form-control form-control-solid" name="gambar[]" accept="image/*" multiple />
                        <div class="text-muted mt-2">Biarkan kosong jika tidak ingin mengubah gambar. Gambar lama akan diganti seluruhnya.</div>
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
<div class="modal fade" id="kt_modal_delete_berita_{{ $berita->uuid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Hapus Berita</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="text-center mb-13">
                    <div class="text-muted fw-semibold fs-5">
                        Apakah Anda yakin ingin menghapus berita <span class="text-danger fw-bold">{{ $berita->namaBerita }}</span>?
                    </div>
                </div>
                <div class="text-center">
                    <form action="{{ route('beritas.destroy', $berita->uuid) }}" method="POST">
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
                {{ $beritas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade text-start" id="kt_modal_add_berita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Berita Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('beritas.store') }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Judul Berita</span></label>
                        <input type="text" class="form-control form-control-solid" name="namaBerita" placeholder="Masukkan judul berita" required />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Penulis (Author)</label>
                        <input type="text" class="form-control form-control-solid" name="author" placeholder="Opsional (Otomatis terisi jika kosong)" />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Keterangan Berita</label>
                        <textarea class="form-control form-control-solid" name="keteranganBerita" rows="6" placeholder="Isi berita"></textarea>
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Gambar (Maksimal 3)</label>
                        <input type="file" class="form-control form-control-solid" name="gambar[]" accept="image/*" multiple />
                        <div class="text-muted mt-2">Anda bisa memilih maksimal 3 gambar (jpg, jpeg, png, gif) ukuran maksimal 2MB per file.</div>
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
