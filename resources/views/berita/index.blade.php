@extends('layouts.front')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-16">
    <div class="mb-12">
        <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight mb-4">Semua Berita & Wawasan</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-3xl">Dapatkan informasi terbaru mengenai teknologi, layanan kami, dan perkembangan industri terkini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($beritas as $berita)
        <a href="{{ route('front.berita.detail', $berita->slugBerita) }}" class="group block bg-surface-container-low rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-outline-variant/30 flex flex-col h-full">
            <div class="aspect-video bg-surface-container overflow-hidden">
                @if(!empty($berita->gambar))
                    <img alt="Berita" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset($berita->gambar[0]) }}"/>
                @else
                    <div class="w-full h-full bg-gray-200"></div>
                @endif
            </div>
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex items-center gap-3 font-label-sm text-label-sm text-on-surface-variant mb-3">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_today</span> {{ $berita->created_at->format('d M Y') }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">person</span> {{ $berita->author }}</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-3 group-hover:text-primary transition-colors line-clamp-2">{{ $berita->namaBerita }}</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-3 mb-4 flex-grow">{{ Str::limit($berita->keteranganBerita, 150) }}</p>
                <div class="inline-flex items-center gap-1 font-label-md text-label-md text-primary font-semibold mt-auto">
                    Baca Selengkapnya <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-12 flex justify-center">
        {{ $beritas->links() }}
    </div>
</div>
@endsection
