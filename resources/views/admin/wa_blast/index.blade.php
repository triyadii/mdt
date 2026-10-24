@extends('layouts.admin')

@section('page_title', 'WhatsApp Blast Promosi')

@section('content')
<div class="d-flex flex-column gap-5">
    <!--begin::Stats Cards-->
    <div class="row g-5 g-xl-8">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-element-11 fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Total Kampanye</div>
                            <div class="fs-2x fw-bold text-gray-900">{{ number_format($stats['total_campaigns']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Seluruh riwayat broadcast dibuat</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-info me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-profile-user fs-2x text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Total Target Nomor</div>
                            <div class="fs-2x fw-bold text-gray-900">{{ number_format($stats['total_recipients']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Kontak yang ditargetkan promosi</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-success me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-check-circle fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Berhasil Terkirim</div>
                            <div class="fs-2x fw-bold text-success">{{ number_format($stats['total_sent']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Pesan promosi sukses terkirim</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-flush h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        <div class="symbol symbol-45px symbol-circle bg-light-danger me-3 d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-cross-circle fs-2x text-danger"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div>
                            <div class="text-muted fw-semibold fs-7">Gagal Terkirim</div>
                            <div class="fs-2x fw-bold text-danger">{{ number_format($stats['total_failed']) }}</div>
                        </div>
                    </div>
                    <span class="text-muted fs-8">Gagal atau nomor tidak valid</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats Cards-->

    <!--begin::Main Card-->
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Daftar Kampanye Promosi WhatsApp</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Kelola pesan broadcast massal untuk penawaran layanan MDT</span>
                </h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('wa-blast.create') }}" class="btn btn-success fw-bold d-flex align-items-center gap-2">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    <span>+ Buat Kampanye Blast</span>
                </a>
            </div>
        </div>

        <div class="card-body py-4">
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-4 mb-5">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
                    <div class="d-flex flex-column">
                        <span class="fw-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center p-4 mb-5">
                    <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <div class="d-flex flex-column">
                        <span class="fw-bold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">No</th>
                            <th class="min-w-200px">Judul Promosi</th>
                            <th class="min-w-250px">Pratinjau Pesan</th>
                            <th class="min-w-150px">Target & Progres</th>
                            <th class="min-w-100px text-center">Status</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="text-end min-w-120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($campaigns as $index => $campaign)
                            @php
                                $percent = $campaign->total_recipients > 0 ? round(($campaign->sent_count / $campaign->total_recipients) * 100) : 0;
                            @endphp
                            <tr>
                                <td>{{ $campaigns->firstItem() + $index }}</td>
                                <td>
                                    <a href="{{ route('wa-blast.show', $campaign->id) }}" class="text-gray-900 text-hover-primary fw-bold fs-6">
                                        {{ $campaign->title }}
                                    </a>
                                    <div class="text-muted fs-8 mt-1">
                                        Jeda: {{ $campaign->delay_seconds }}s/pesan
                                        @if($campaign->creator)
                                            • Dibuat oleh {{ $campaign->creator->nama }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-gray-700 fs-7 text-truncate" style="max-width: 280px;" title="{{ $campaign->message }}">
                                        {{ Str::limit($campaign->message, 80) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column w-100 me-2">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-muted fs-8">{{ $campaign->sent_count }} / {{ $campaign->total_recipients }} terkirim</span>
                                            <span class="text-muted fs-8 fw-bold">{{ $percent }}%</span>
                                        </div>
                                        <div class="progress h-6px w-100 bg-light">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        @if($campaign->failed_count > 0)
                                            <span class="text-danger fs-9 mt-1">{{ $campaign->failed_count }} nomor gagal</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($campaign->status === 'completed')
                                        <span class="badge badge-light-success fw-bold">Selesai</span>
                                    @elseif($campaign->status === 'processing')
                                        <span class="badge badge-light-primary fw-bold">Sedang Berjalan</span>
                                    @else
                                        <span class="badge badge-light-warning fw-bold">Draft / Siap</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted fs-7">{{ $campaign->created_at->format('d M Y') }}</span>
                                    <div class="text-muted fs-8">{{ $campaign->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('wa-blast.show', $campaign->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Buka & Kirim Blast">
                                            <i class="ki-duotone ki-send fs-4"><span class="path1"></span><span class="path2"></span></i>
                                        </a>

                                        <form action="{{ route('wa-blast.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kampanye blast ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Hapus Kampanye">
                                                <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-muted">
                                    <i class="ki-duotone ki-send fs-3x text-gray-400 mb-3 d-block"><span class="path1"></span><span class="path2"></span></i>
                                    <div class="fw-semibold">Belum ada kampanye WA Blast yang dibuat.</div>
                                    <div class="fs-8 mt-1">Mulai promosi layanan MDT sekarang dengan menekan tombol <strong>+ Buat Kampanye Blast</strong> di atas.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
                {{ $campaigns->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    <!--end::Main Card-->
</div>
@endsection
