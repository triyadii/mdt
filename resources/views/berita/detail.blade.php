@extends('layouts.front')

@section('content')
<div class="max-w-4xl mx-auto px-6 lg:px-12 py-16">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant mb-8">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('front.berita') }}" class="hover:text-primary transition-colors">Berita</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-on-surface line-clamp-1">{{ $berita->namaBerita }}</span>
    </nav>

    <!-- Header -->
    <div class="mb-10">
        <div class="flex flex-wrap items-center gap-4 font-label-sm text-label-sm text-on-surface-variant mb-4">
            <span class="flex items-center gap-1.5 px-3 py-1 bg-surface-container rounded-full text-on-surface"><span class="material-symbols-outlined text-[16px]">calendar_today</span> {{ $berita->created_at->format('d F Y') }}</span>
            <span class="flex items-center gap-1.5 px-3 py-1 bg-surface-container rounded-full text-on-surface"><span class="material-symbols-outlined text-[16px]">person</span> {{ $berita->author }}</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg-mobile md:text-[48px] md:leading-[56px] text-on-surface font-bold tracking-tight mb-6">
            {{ $berita->namaBerita }}
        </h1>
    </div>

    <!-- Featured Image -->
    @if(!empty($berita->gambar))
        <div class="w-full aspect-video rounded-2xl overflow-hidden bg-surface-container mb-12 shadow-sm">
            <img src="{{ asset($berita->gambar[0]) }}" alt="{{ $berita->namaBerita }}" class="w-full h-full object-cover" />
        </div>
    @endif

    <!-- Content -->
    <article class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed space-y-6 bg-surface-container-low/50 p-8 md:p-12 rounded-3xl" style="text-align: justify; white-space: pre-line;">{{ trim($berita->keteranganBerita) }}</article>

    <!-- Other Images Gallery (If multiple) -->
    @if(!empty($berita->gambar) && count($berita->gambar) > 1)
        <div class="mt-12">
            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-6">Galeri Gambar</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($berita->gambar as $index => $gbr)
                    @if($index > 0)
                        <div class="aspect-square rounded-xl overflow-hidden bg-surface-container">
                            <img src="{{ asset($gbr) }}" class="w-full h-full object-cover hover:scale-105 transition-transform" />
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Related News -->
    @if($otherBeritas->count() > 0)
    <div class="mt-20 pt-12 border-t border-outline-variant/30">
        <div class="flex items-center justify-between mb-8">
            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Berita Terkait</h3>
            <a href="{{ route('front.berita') }}" class="font-label-md text-label-md text-primary hover:text-primary-fixed transition-colors">Lihat Semua</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($otherBeritas as $other)
            <a href="{{ route('front.berita.detail', $other->slugBerita) }}" class="group block bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex flex-col h-full">
                <div class="aspect-video bg-surface-container overflow-hidden">
                    @if(!empty($other->gambar))
                        <img src="{{ asset($other->gambar[0]) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    @else
                        <div class="w-full h-full bg-gray-200"></div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <span class="font-label-sm text-label-sm text-on-surface-variant mb-2">{{ $other->created_at->format('d M Y') }}</span>
                    <h4 class="font-label-md text-label-md text-on-surface font-bold mb-2 group-hover:text-primary transition-colors line-clamp-2">{{ $other->namaBerita }}</h4>
                    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 mt-auto">{{ Str::limit($other->keteranganBerita, 80) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
