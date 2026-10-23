@extends('layouts.front')

@php
    $coverImage = (!empty($berita->gambar) && is_array($berita->gambar) && count($berita->gambar) > 0) ? asset($berita->gambar[0]) : asset('assets/media/logos/logo2.png');
    $excerpt = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($berita->keteranganBerita))), 155);

    $schemaArticle = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'NewsArticle',
                'headline' => $berita->namaBerita,
                'image' => [$coverImage],
                'datePublished' => $berita->created_at->toIso8601String(),
                'dateModified' => $berita->updated_at->toIso8601String(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $berita->author ?? 'MDT Solution Indonesia',
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'MDT Solution Indonesia',
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('assets/media/logos/logo2.png'),
                    ],
                ],
                'description' => $excerpt,
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => route('front.berita.detail', $berita->slugBerita),
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => route('home'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Berita',
                        'item' => route('front.berita'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $berita->namaBerita,
                        'item' => route('front.berita.detail', $berita->slugBerita),
                    ],
                ],
            ],
        ],
    ];
@endphp

@section('title', $berita->namaBerita . ' - MDT Solution Indonesia')
@section('meta_title', $berita->namaBerita . ' - MDT Solution Indonesia')
@section('meta_description', $excerpt)
@section('meta_author', $berita->author ?? 'MDT Solution Indonesia')
@section('canonical_url', route('front.berita.detail', $berita->slugBerita))
@section('og_type', 'article')
@section('og_url', route('front.berita.detail', $berita->slugBerita))
@section('og_title', $berita->namaBerita . ' - MDT Solution Indonesia')
@section('og_description', $excerpt)
@section('og_image', $coverImage)
@section('twitter_url', route('front.berita.detail', $berita->slugBerita))
@section('twitter_title', $berita->namaBerita . ' - MDT Solution Indonesia')
@section('twitter_description', $excerpt)
@section('twitter_image', $coverImage)

@section('schema_json')
<script type="application/ld+json">
{!! json_encode($schemaArticle, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-16">
    <div class="flex flex-col lg:flex-row gap-12">
        <!-- Left Column: News Detail -->
        <div class="w-full lg:w-2/3">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant mb-8" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <a href="{{ route('front.berita') }}" class="hover:text-primary transition-colors">Berita</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="text-on-surface line-clamp-1">{{ $berita->namaBerita }}</span>
            </nav>

            <!-- Title -->
            <h1 class="font-headline-lg text-headline-lg-mobile md:text-[48px] md:leading-[56px] text-on-surface font-bold tracking-tight mb-6">
                {{ $berita->namaBerita }}
            </h1>

            <!-- Featured Image -->
            @if(!empty($berita->gambar) && is_array($berita->gambar) && count($berita->gambar) > 0)
                <div class="w-full aspect-video rounded-2xl overflow-hidden bg-surface-container mb-6 shadow-sm">
                    <img src="{{ asset($berita->gambar[0]) }}" alt="{{ $berita->namaBerita }}" loading="eager" decoding="async" class="w-full h-full object-cover" />
                </div>
            @endif

            <!-- Meta Info (Published Date & Author) -->
            <div class="flex flex-wrap items-center gap-4 font-label-sm text-label-sm text-on-surface-variant mb-8">
                <span class="flex items-center gap-1.5 px-3 py-1 bg-surface-container rounded-full text-on-surface"><span class="material-symbols-outlined text-[16px]">calendar_today</span> {{ $berita->created_at->format('d F Y') }}</span>
                <span class="flex items-center gap-1.5 px-3 py-1 bg-surface-container rounded-full text-on-surface"><span class="material-symbols-outlined text-[16px]">person</span> {{ $berita->author }}</span>
            </div>

            <!-- Content -->
            <article class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed space-y-6 bg-surface-container-low/50 p-8 md:p-12 rounded-3xl" style="text-align: justify; white-space: pre-line;">{{ trim($berita->keteranganBerita) }}</article>

            <!-- Other Images Gallery (If multiple) -->
            @if(!empty($berita->gambar) && is_array($berita->gambar) && count($berita->gambar) > 1)
                <div class="mt-12">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-6">Galeri Gambar</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($berita->gambar as $index => $gbr)
                            @if($index > 0)
                                <div class="aspect-square rounded-xl overflow-hidden bg-surface-container">
                                    <img src="{{ asset($gbr) }}" alt="{{ $berita->namaBerita }} - Gambar {{ $index + 1 }}" loading="lazy" decoding="async" class="w-full h-full object-cover hover:scale-105 transition-transform" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Other News Sidebar -->
        <div class="w-full lg:w-1/3">
            @if($otherBeritas->count() > 0)
            <div class="sticky top-28">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Berita Terkait</h3>
                    <a href="{{ route('front.berita') }}" class="font-label-md text-label-md text-primary hover:text-primary-fixed transition-colors">Lihat Semua</a>
                </div>
                
                <div class="flex flex-col gap-4">
                    @foreach($otherBeritas as $other)
                    <a href="{{ route('front.berita.detail', $other->slugBerita) }}" class="group flex gap-4 bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border border-outline-variant/30 p-3">
                        <div class="w-24 h-24 flex-shrink-0 bg-surface-container rounded-lg overflow-hidden">
                            @if(!empty($other->gambar) && is_array($other->gambar) && count($other->gambar) > 0)
                                <img src="{{ asset($other->gambar[0]) }}" alt="{{ $other->namaBerita }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            @else
                                <div class="w-full h-full bg-gray-200"></div>
                            @endif
                        </div>
                        <div class="flex flex-col justify-center">
                            <span class="font-label-sm text-label-sm text-on-surface-variant mb-1">{{ $other->created_at->format('d M Y') }}</span>
                            <h4 class="font-label-md text-label-md text-on-surface font-bold group-hover:text-primary transition-colors line-clamp-2">{{ $other->namaBerita }}</h4>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
