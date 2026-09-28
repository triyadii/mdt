import re

def replace_section(html, start_marker, end_marker, replacement):
    pattern = re.compile(re.escape(start_marker) + r'.*?' + re.escape(end_marker), re.DOTALL)
    if pattern.search(html):
        return pattern.sub(replacement, html)
    else:
        print(f"Warning: Could not find section to replace starting with {start_marker}")
        return html

with open('resources/views/welcome.blade.php', 'r', encoding='utf-8') as f:
    html = f.read()

# 1. Company Profile
html = html.replace('MDT Solution Indonesia - Jasa Pembuatan Website, Aplikasi Mobile &amp; CCTV Medan', '{{ $company->nama_profil ?? "MDT Solution" }} - Solusi IT Terbaik')

# 2. About
about_text = "MDT Solution Indonesia berdiri sebagai konsultan dan mitra rekayasa teknologi terpercaya yang memadukan keahlian teknis kelas dunia dengan pemahaman mendalam tentang lanskap bisnis modern. Kami tidak sekadar membangun aplikasi; kami merancang ekosistem komputasi adaptif yang memungkinkan perusahaan beroperasi tanpa hambatan di era ketidakpastian pasar."
html = html.replace(about_text, '{{ $about->keterangan_tentang ?? "MDT Solution berdiri sebagai konsultan..." }}')

# 3. Mitra
mitra_pattern = r'<!-- Client Brand Trust Ticker Banner -->.*?<div aria-hidden="true" class="flex min-w-full animate-marquee gap-8 md:gap-16 items-center px-4 md:px-8 absolute top-0 left-full">.*?</div>'
mitra_replacement = r'''<!-- Client Brand Trust Ticker Banner -->
<div class="flex overflow-hidden relative">
    <div class="flex min-w-full animate-marquee gap-8 md:gap-16 items-center px-4 md:px-8">
        @foreach($partners as $partner)
            <div class="font-headline-sm text-headline-sm text-on-surface-variant font-bold opacity-60 hover:opacity-100 transition-opacity">
                {{ $partner->nama_perusahaan }}
            </div>
        @endforeach
    </div>
    <div aria-hidden="true" class="flex min-w-full animate-marquee gap-8 md:gap-16 items-center px-4 md:px-8 absolute top-0 left-full">
        @foreach($partners as $partner)
            <div class="font-headline-sm text-headline-sm text-on-surface-variant font-bold opacity-60 hover:opacity-100 transition-opacity">
                {{ $partner->nama_perusahaan }}
            </div>
        @endforeach
    </div>'''
html = re.sub(mitra_pattern, mitra_replacement, html, flags=re.DOTALL)

# 4. Keunggulan
keunggulan = """<!-- Bento Grid (6 Pillars) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($keunggulans as $keunggulan)
    <div class="p-8 rounded-xl bg-surface-container-low/70 backdrop-blur-md shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary mb-5">
            <span class="material-symbols-outlined text-[26px]">star</span>
        </div>
        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-3">{{ $keunggulan->namaUnggulan }}</h3>
        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
            {{ $keunggulan->keteranganUnggulan }}
        </p>
    </div>
    @endforeach
</div>
</section>"""
html = replace_section(html, '<!-- Bento Grid (6 Pillars) -->', '</section>', keunggulan)

# 5. Jasa
jasa = """<!-- Bento Grid (4 Service Cards) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-14">
    @foreach($jasas as $jasa)
    <div class="group relative overflow-hidden rounded-2xl bg-surface-container-low/80 backdrop-blur-md p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-outline-variant/30 flex flex-col h-full">
        <div class="w-14 h-14 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-[32px]">devices</span>
        </div>
        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-3">{{ $jasa->namaJasa }}</h3>
        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-8 flex-grow">
            {{ $jasa->keteranganJasa }}
        </p>
    </div>
    @endforeach
</div>
</div>
</section>"""
html = replace_section(html, '<!-- Bento Grid (4 Service Cards) -->', '</section>', jasa)

# 6. Portofolio
portofolio = """<!-- Bento Grid Projects -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($portfolios as $portfolio)
    <div class="group relative rounded-2xl overflow-hidden bg-surface-container-low/50 border border-outline-variant/30 shadow-sm hover:shadow-xl transition-all duration-300">
        <div class="aspect-video bg-surface-container overflow-hidden">
            @if(!empty($portfolio->gambar))
                <img alt="Project" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset($portfolio->gambar[0]) }}"/>
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">No Image</div>
            @endif
        </div>
        <div class="p-6">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-secondary-container/50 text-on-secondary-container font-label-sm text-label-sm mb-3">
                <span>{{ $portfolio->type->nama_jenis ?? $portfolio->jenis_project }}</span>
            </div>
            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2">{{ $portfolio->nama_project }}</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mb-4 line-clamp-2">
                {{ $portfolio->keterangan_project }}
            </p>
            @if($portfolio->link_project)
            <a class="inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:text-primary-fixed transition-colors" href="{{ $portfolio->link_project }}" target="_blank">
                Lihat Case Study <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
            @endif
        </div>
    </div>
    @endforeach
</div>
</div>
</section>"""
html = replace_section(html, '<!-- Bento Grid Projects -->', '</section>', portofolio)

# 7. Testimoni
testimoni = """<!-- Testimonial Carousel Track Container -->
<div class="flex transition-transform duration-500 ease-out" id="testimonial-track">
    @foreach($testimonials as $testi)
    <div class="min-w-full px-4 lg:px-12 flex-shrink-0">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-8 md:gap-12">
            <div class="flex-1 space-y-6">
                <p class="font-headline-sm text-headline-sm-mobile md:text-headline-sm text-on-surface font-medium leading-relaxed italic">
                    "{{ $testi->keterangan_testimoni }}"
                </p>
                <div>
                    <h4 class="font-label-md text-label-md text-on-surface font-bold">{{ $testi->nama_testimoni }}</h4>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
</div>
<!-- Nav Dots -->
<div class="flex items-center justify-center gap-3 mt-12">
    @foreach($testimonials as $index => $testi)
    <button aria-label="Slide {{ $index + 1 }}" class="testimonial-dot w-3 h-3 rounded-full {{ $index == 0 ? 'bg-primary' : 'bg-surface-container-high' }} transition-all" onclick="goToTestimonial({{ $index }})" type="button"></button>
    @endforeach
</div>
</div>
</section>"""
html = replace_section(html, '<!-- Testimonial Carousel Track Container -->', '</section>', testimoni)

# 8. FAQ
faq = """<!-- FAQ Items Container -->
<div class="space-y-4">
    @foreach($faqs as $index => $faq)
    <details class="group bg-surface-container-low rounded-xl border border-outline-variant/30 [&_summary::-webkit-details-marker]:hidden" {{ $index == 0 ? 'open' : '' }}>
        <summary class="flex items-center justify-between p-6 cursor-pointer font-headline-sm text-headline-sm text-on-surface font-semibold">
            <span>{{ $faq->pertanyaan }}</span>
            <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
        </summary>
        <div class="px-6 pb-6 pt-0 font-body-md text-body-md text-on-surface-variant leading-relaxed">
            {{ $faq->jawaban }}
        </div>
    </details>
    @endforeach
</div>
</div>
</section>"""
html = replace_section(html, '<!-- FAQ Items Container -->', '</section>', faq)

# 9. Berita
berita = """<!-- Bento Grid Layout for News (1 Large + 2 Small) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($beritas as $berita)
    <a href="{{ route('front.berita.detail', $berita->slugBerita) }}" class="group block bg-surface-container-low rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-outline-variant/30">
        <div class="aspect-video bg-surface-container overflow-hidden">
            @if(!empty($berita->gambar))
                <img alt="Berita" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset($berita->gambar[0]) }}"/>
            @else
                <div class="w-full h-full bg-gray-200"></div>
            @endif
        </div>
        <div class="p-6">
            <div class="flex items-center gap-3 font-label-sm text-label-sm text-on-surface-variant mb-3">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_today</span> {{ $berita->created_at->format('d M Y') }}</span>
            </div>
            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-3 group-hover:text-primary transition-colors line-clamp-2">{{ $berita->namaBerita }}</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-3">{{ Str::limit($berita->keteranganBerita, 100) }}</p>
        </div>
    </a>
    @endforeach
</div>
</div>
</section>"""
html = replace_section(html, '<!-- Bento Grid Layout for News (1 Large + 2 Small) -->', '</section>', berita)

# 10. Edit "Lihat Semua Berita" link
html = html.replace('href="#news"', 'href="{{ route(\'front.berita\') }}"')

with open('resources/views/welcome.blade.php', 'w', encoding='utf-8') as f:
    f.write(html)
