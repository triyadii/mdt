@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <h3 class="card-title">Selamat Datang di Sistem Manajemen MDT</h3>
        <p>Gunakan menu di sidebar untuk menavigasi aplikasi.</p>
    </div>
</div>

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Daftar Kunjungan</span>
                <span class="text-muted mt-1 fw-semibold fs-7">Total {{ $visitors->total() }} kunjungan</span>
            </h3>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_visitors">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2">No</th>
                        <th class="min-w-125px">IP Address</th>
                        <th class="min-w-125px">Tanggal Kunjungan</th>
                        <th class="min-w-125px">Waktu</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($visitors as $index => $visitor)
                    <tr>
                        <td>{{ $visitors->firstItem() + $index }}</td>
                        <td>{{ $visitor->ip_address }}</td>
                        <td>{{ $visitor->visited_date ? \Carbon\Carbon::parse($visitor->visited_date)->format('d M Y') : $visitor->created_at->format('d M Y') }}</td>
                        <td>{{ $visitor->created_at->format('H:i:s') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data kunjungan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
            {{ $visitors->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
