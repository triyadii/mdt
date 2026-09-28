@extends('layouts.admin')

@section('page_title', 'Manajemen Portofolio')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Manajemen Portofolio</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Kelola daftar project portofolio</span>
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_portfolio">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Portofolio
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

        @if(session('error'))
            <div class="alert alert-danger p-5 mb-10">
                {{ session('error') }}
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
                        <th class="min-w-150px">Gambar</th>
                        <th class="min-w-200px">Nama Project</th>
                        <th class="min-w-150px">Jenis Project</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($portfolios as $key => $portfolio)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold fs-6">{{ $key + 1 }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if(!empty($portfolio->gambar) && is_array($portfolio->gambar) && count($portfolio->gambar) > 0)
                                    <div class="symbol symbol-50px me-3">
                                        <img src="{{ asset('storage/' . $portfolio->gambar[0]) }}" alt="img" class="object-fit-cover"/>
                                    </div>
                                    @if(count($portfolio->gambar) > 1)
                                        <span class="badge badge-light-primary">+{{ count($portfolio->gambar) - 1 }}</span>
                                    @endif
                                @else
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label fs-3 bg-light-danger text-danger">X</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-bold d-block fs-6">{{ $portfolio->nama_project }}</span>
                            @if($portfolio->link_project)
                                <a href="{{ $portfolio->link_project }}" target="_blank" class="text-primary fs-7">Lihat Link</a>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-light-info fs-7 fw-bold">{{ $portfolio->type ? $portfolio->type->nama_jenis : '-' }}</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_detail_portfolio_{{ $portfolio->uuid }}" title="Lihat Detail">
                                <i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_portfolio_{{ $portfolio->uuid }}" title="Edit">
                                <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_portfolio_{{ $portfolio->uuid }}" title="Hapus">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Detail Modal -->
                    <div class="modal fade" id="kt_modal_detail_portfolio_{{ $portfolio->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-900px">
                            <div class="modal-content rounded-4">
                                <div class="modal-header border-0 pb-0 justify-content-end">
                                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-2x"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y pt-0 pb-15 px-5 px-xl-15">
                                    <div class="row gx-10">
                                        <!-- Left Column: Images -->
                                        <div class="col-lg-5 mb-10 mb-lg-0">
                                            @if(!empty($portfolio->gambar) && is_array($portfolio->gambar))
                                                @foreach($portfolio->gambar as $index => $img)
                                                    @if($index == 0)
                                                        <!-- Main image -->
                                                        <a href="{{ asset('storage/' . $img) }}" target="_blank" class="d-block bgi-no-repeat bgi-position-center bgi-size-cover rounded-4 shadow-sm mb-4" style="background-image:url('{{ asset('storage/' . $img) }}'); height: 300px;"></a>
                                                    @endif
                                                @endforeach
                                                @if(count($portfolio->gambar) > 1)
                                                    <div class="d-flex gap-4">
                                                        @foreach(array_slice($portfolio->gambar, 1) as $img)
                                                        <a href="{{ asset('storage/' . $img) }}" target="_blank" class="d-block bgi-no-repeat bgi-position-center bgi-size-cover rounded-4 shadow-sm h-100px w-100" style="background-image:url('{{ asset('storage/' . $img) }}'); flex: 1;"></a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @else
                                                <div class="rounded-4 bg-light-dark d-flex flex-column justify-content-center align-items-center h-300px mb-4 shadow-sm">
                                                    <i class="ki-duotone ki-picture fs-5x text-gray-400 mb-3"><span class="path1"></span><span class="path2"></span></i>
                                                    <div class="text-gray-500 fw-bold fs-5">Tidak ada gambar</div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Right Column: Details -->
                                        <div class="col-lg-7">
                                            <!-- Title & Type -->
                                            <div class="d-flex flex-column mb-7">
                                                <h1 class="text-gray-900 fw-bolder fs-2tx mb-3">{{ $portfolio->nama_project }}</h1>
                                                <div class="d-flex flex-wrap align-items-center gap-3">
                                                    <span class="badge badge-light-primary fs-5 fw-bold py-2 px-3">
                                                        <i class="ki-duotone ki-category fs-4 text-primary me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                                        {{ $portfolio->type ? $portfolio->type->nama_jenis : 'Tanpa Kategori' }}
                                                    </span>
                                                    @if($portfolio->link_project)
                                                        <a href="{{ $portfolio->link_project }}" target="_blank" class="btn btn-sm btn-light-info d-flex align-items-center py-2 px-3">
                                                            <i class="ki-duotone ki-fasten fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> Kunjungi Link
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div class="mb-8">
                                                <h3 class="text-gray-800 fw-bold mb-4 fs-3">Tentang Project</h3>
                                                <div class="text-gray-600 fs-5 lh-xl" style="white-space: pre-wrap; text-align: justify;">{{ $portfolio->keterangan_project }}</div>
                                            </div>
                                            
                                            <!-- Meta Info -->
                                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded-3 p-5 bg-lighten">
                                                <i class="ki-duotone ki-calendar-8 fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-500 fw-semibold fs-6">Ditambahkan Pada</span>
                                                    <span class="text-gray-800 fw-bold fs-4">{{ $portfolio->created_at->translatedFormat('d F Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade text-start" id="kt_modal_edit_portfolio_{{ $portfolio->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-750px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Edit Portofolio</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <form action="{{ route('portfolios.update', $portfolio->uuid) }}" method="POST" class="form" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Project</span></label>
                                            <input type="text" class="form-control form-control-solid" name="nama_project" value="{{ $portfolio->nama_project }}" required />
                                        </div>
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Jenis Project</span></label>
                                            <select name="jenis_project" class="form-select form-select-solid" required>
                                                <option value="">Pilih Jenis Project</option>
                                                @foreach($projectTypes as $type)
                                                    <option value="{{ $type->uuid }}" {{ $portfolio->jenis_project == $type->uuid ? 'selected' : '' }}>{{ $type->nama_jenis }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Keterangan Project</span></label>
                                            <textarea class="form-control form-control-solid" name="keterangan_project" rows="4" required>{{ $portfolio->keterangan_project }}</textarea>
                                        </div>
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Link Project</label>
                                            <input type="url" class="form-control form-control-solid" name="link_project" value="{{ $portfolio->link_project }}" placeholder="https://..." />
                                        </div>
                                        
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Gambar Saat Ini (Centang untuk menghapus)</label>
                                            <div class="d-flex flex-wrap gap-5">
                                                @if(!empty($portfolio->gambar) && is_array($portfolio->gambar))
                                                    @foreach($portfolio->gambar as $img)
                                                    <div class="border p-2 rounded text-center">
                                                        <img src="{{ asset('storage/' . $img) }}" style="width: 100px; height: 100px; object-fit: cover;" class="rounded mb-2 d-block" />
                                                        <div class="form-check form-check-custom form-check-solid form-check-danger d-inline-block">
                                                            <input class="form-check-input" type="checkbox" name="hapus_gambar[]" value="{{ $img }}" />
                                                            <label class="form-check-label fs-8">Hapus</label>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-muted">Belum ada gambar.</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Tambah Gambar (Total max 3)</label>
                                            <input type="file" class="form-control form-control-solid" name="gambar[]" multiple accept="image/*" />
                                            <div class="text-muted fs-7 mt-2">Pilih hingga 3 gambar (Format: JPEG, PNG, JPG).</div>
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
                    <div class="modal fade text-start" id="kt_modal_delete_portfolio_{{ $portfolio->uuid }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-400px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Hapus Portofolio</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-danger" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="modal-body text-center py-10">
                                    <div class="mb-5">
                                        <i class="ki-duotone ki-warning fs-5x text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </div>
                                    <h4>Apakah Anda yakin ingin menghapus <strong>{{ $portfolio->nama_project }}</strong>?</h4>
                                    <p class="text-muted mt-3">Gambar terkait juga akan dihapus dari sistem.</p>
                                    <div class="mt-10">
                                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('portfolios.destroy', $portfolio->uuid) }}" method="POST" style="display:inline-block;">
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
                        <td colspan="5" class="text-center text-muted">Belum ada data portofolio.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Portfolio -->
<div class="modal fade" id="kt_modal_add_portfolio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Portofolio</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('portfolios.store') }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Nama Project</span></label>
                        <input type="text" class="form-control form-control-solid" name="nama_project" required />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Jenis Project</span></label>
                        <select name="jenis_project" class="form-select form-select-solid" required>
                            <option value="">Pilih Jenis Project</option>
                            @foreach($projectTypes as $type)
                                <option value="{{ $type->uuid }}">{{ $type->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2"><span class="required">Keterangan Project</span></label>
                        <textarea class="form-control form-control-solid" name="keterangan_project" rows="4" required></textarea>
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Link Project</label>
                        <input type="url" class="form-control form-control-solid" name="link_project" placeholder="https://..." />
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Upload Gambar (Maks 3)</label>
                        <input type="file" class="form-control form-control-solid" name="gambar[]" multiple accept="image/*" />
                        <div class="text-muted fs-7 mt-2">Pilih hingga 3 gambar (Format: JPEG, PNG, JPG).</div>
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
