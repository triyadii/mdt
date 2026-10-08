@extends('layouts.front')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-12 py-16">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
        <div class="max-w-2xl">
            <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight mb-4">Semua Portfolio</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-3xl">Jelajahi implementasi nyata solusi teknologi MDT Solution Indonesia yang telah menghasilkan peningkatan efisiensi operasional terukur.</p>
        </div>
        
        <!-- Filter Tab Buttons -->
        <div class="flex flex-wrap gap-2 p-1.5 rounded-lg bg-surface-container">
            <button class="portfolio-filter-btn px-4 py-2 rounded font-label-md text-label-md bg-surface-container-lowest text-primary font-semibold shadow-sm transition-all" onclick="filterPortfolio('all', this)" type="button">All</button>
            @foreach($projectTypes as $ptype)
            <button class="portfolio-filter-btn px-4 py-2 rounded font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-all" onclick="filterPortfolio('{{ $ptype->uuid }}', this)" type="button">{{ $ptype->nama_jenis }}</button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($portfolios as $portfolio)
        <div class="portfolio-item group relative rounded-2xl overflow-hidden bg-surface-container-low/50 border border-outline-variant/30 shadow-sm hover:shadow-xl transition-all duration-300" data-categories="{{ $portfolio->jenis_project }}">
            <div class="aspect-video bg-surface-container overflow-hidden">
                @if(!empty($portfolio->gambar) && is_array($portfolio->gambar) && count($portfolio->gambar) > 0)
                    <img alt="Project" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset('storage/' . $portfolio->gambar[0]) }}"/>
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">No Image</div>
                @endif
            </div>
            <div class="p-6 flex flex-col flex-grow">
                <div class="inline-flex self-start items-center gap-1.5 px-2.5 py-1 rounded bg-secondary-container/50 text-on-secondary-container font-label-sm text-label-sm mb-3">
                    <span>{{ $portfolio->type->nama_jenis ?? $portfolio->jenis_project }}</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2">{{ $portfolio->nama_project }}</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mb-4 line-clamp-3 flex-grow">
                    {{ $portfolio->keterangan_project }}
                </p>
                @if($portfolio->link_project)
                <a class="inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:text-primary-fixed transition-colors mt-auto" href="{{ $portfolio->link_project }}" target="_blank">
                    Lihat Case Study <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginasi jika menggunakan paginate, tetapi filter js butuh all items -->
    @if(method_exists($portfolios, 'links'))
    <div class="mt-12 flex justify-center">
        {{ $portfolios->links() }}
    </div>
    @endif
</div>

<script>
    function filterPortfolio(category, btnElement) {
      // Update button active state
      document.querySelectorAll('.portfolio-filter-btn').forEach(b => {
        b.className = 'portfolio-filter-btn px-4 py-2 rounded font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-all';
      });
      btnElement.className = 'portfolio-filter-btn px-4 py-2 rounded font-label-md text-label-md bg-surface-container-lowest text-primary font-semibold shadow-sm transition-all';

      // Filter card items
      const items = document.querySelectorAll('.portfolio-item');
      items.forEach(item => {
        const itemCategories = item.getAttribute('data-categories') || '';
        if (category === 'all' || itemCategories.includes(category)) {
          item.classList.remove('hidden');
        } else {
          item.classList.add('hidden');
        }
      });
    }
</script>
@endsection
