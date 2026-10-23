<?php

$f = '/var/www/html/WebsiteBiantaraGemilangMedia/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Get head and header
$headerEnd = strpos($c, '</header>') + 9;
$headerHtml = substr($c, 0, $headerEnd);

// Add the hamburger button before the last </div></div></header> in $headerHtml
$searchForBtn = '</div></div></header>';
$btnHtml = '
<button id="mobile-menu-btn" class="xl:hidden flex items-center justify-center p-2 text-on-surface hover:bg-surface-container rounded-lg focus:outline-none">
  <span class="material-symbols-outlined">menu</span>
</button>
</div>
<!-- MOBILE MENU -->
<div id="mobile-menu" class="hidden xl:hidden w-full absolute top-20 left-0 bg-surface-container-lowest border-t border-outline-variant/30 flex-col py-4 px-margin gap-4 shadow-lg z-40">
  <a {!! request()->is(\'/\') ? \'class="font-label-lg text-secondary"\' : \'class="font-label-lg text-on-surface"\' !!} href="{{ url(\'/\') }}">Beranda</a>
  <a {!! request()->is(\'Katalog*\') ? \'class="font-label-lg text-secondary"\' : \'class="font-label-lg text-on-surface"\' !!} href="{{ url(\'/Katalog\') }}">Katalog Lokasi OOH</a>
  <a {!! request()->is(\'TentangKami\') ? \'class="font-label-lg text-secondary"\' : \'class="font-label-lg text-on-surface"\' !!} href="{{ url(\'/TentangKami\') }}">Tentang Kami</a>
  <a {!! request()->is(\'Portfolio\') ? \'class="font-label-lg text-secondary"\' : \'class="font-label-lg text-on-surface"\' !!} href="{{ url(\'/Portfolio\') }}">Layanan &amp; Portofolio</a>
  <a {!! request()->is(\'Kontak\') ? \'class="font-label-lg text-secondary"\' : \'class="font-label-lg text-on-surface"\' !!} href="{{ url(\'/Kontak\') }}">Kontak</a>
</div>
</div></header>
';
$headerHtml = str_replace($searchForBtn, $btnHtml, $headerHtml);

// 2. The main body
$bodyHtml = '
<main class="w-full pt-20 bg-surface min-h-screen">
  @yield(\'content\')
</main>
';

// 3. The footer and scripts
$footerHtml = <<<'HTML'
<aside class="fixed bottom-6 right-6 z-40 flex items-center"><a class="flex items-center gap-space-sm bg-surface-container-lowest text-on-surface px-space-md py-space-sm rounded-full shadow-[0_10px_25px_-5px_rgba(0,0,0,0.15)] hover:bg-surface-container transition-all group" href="https://wa.me/6281288997700?text=Halo%20PT%20Biantara%20Gemilang%20Media,%20saya%20ingin%20tanya%20titik%20OOH" rel="noopener noreferrer" target="_blank"><span class="flex h-3 w-3 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span></span><span class="font-label-md text-label-md font-semibold">Tanya Ketersediaan Titik</span><span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span></a></aside>
<footer class="w-full bg-surface-container-low mt-space-xl pt-space-xl pb-space-lg"><div class="w-full px-margin"><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-space-lg mb-space-xl"><div class="lg:col-span-4 flex flex-col gap-space-md"><div class="flex items-center gap-space-sm"><img alt="Logo PT Biantara Gemilang Media" class="h-8 w-auto object-contain" src="https://lh3.googleusercontent.com/aida/AEtjO1X5aX4ekhR87npjmAFgU7uT2f6Vm-pcyva2H_pltl9w9GdsErn2-XI4QSu6R4FW7RYsZ_570gRX69jOG-hnajlIEnrDPOJdVxwBj5eZDNc4uKpeWZvWpL4IR63GsJ9OeCq3sGdzYL7dbK7biZ5AjKMCoFccbEtX9xW4GrO4193pSrqlrfVKoQ2sxyPNzhipFumRMEFMxUvbli_3oYvZ8zu-HE5z7bI57fBwGYWiK5aNgmgGSokAfEYL0lKk"/><span class="font-headline-sm text-headline-sm text-on-surface">PT Biantara Gemilang Media</span></div><p class="font-body-md text-body-md text-on-surface-variant max-w-md">Penyedia Solusi Iklan Luar Ruang (OOH/Billboard/Videotron) Terpercaya dengan 800+ Titik Strategis di Seluruh Indonesia.</p><div class="flex flex-col gap-space-xs font-body-sm text-body-sm text-on-surface-variant"><div class="flex items-center gap-space-xs"><span class="material-symbols-outlined text-[18px] text-secondary">call</span><span>WhatsApp: +62 812-8899-7700</span></div><div class="flex items-center gap-space-xs"><span class="material-symbols-outlined text-[18px] text-secondary">mail</span><span>Email: sales@biantaragemilang.co.id</span></div></div></div><div class="lg:col-span-3 flex flex-col gap-space-sm"><span class="font-label-lg text-label-lg text-on-surface uppercase tracking-wider">Quick Links</span><ul class="flex flex-col gap-space-xs font-body-md text-body-md text-on-surface-variant"><li><a class="hover:text-secondary transition-colors" data-path="katalog-lokasi-ooh" href="{{ url('/Katalog') }}">Katalog OOH</a></li><li><a class="hover:text-secondary transition-colors" data-path="tentang-kami" href="{{ url('/TentangKami') }}">Tentang Kami</a></li><li><a class="hover:text-secondary transition-colors" data-path="layanan-portofolio" href="{{ url('/Portfolio') }}">Layanan &amp; Portofolio</a></li><li><a class="hover:text-secondary transition-colors" data-path="kontak" href="{{ url('/Kontak') }}">Kontak</a></li></ul></div><div class="lg:col-span-5 flex flex-col gap-space-sm"><span class="font-label-lg text-label-lg text-on-surface uppercase tracking-wider">Kantor Pusat &amp; Hub Operasional</span><div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md font-body-sm text-body-sm text-on-surface-variant"><div><span class="font-label-md text-label-md text-on-surface block">Kantor Pusat Jakarta</span><p>Jl. Jend. Sudirman Kav. 52-53, SCBD, Jakarta Selatan 12190</p></div><div><span class="font-label-md text-label-md text-on-surface block">Hub Surabaya &amp; Jatim</span><p>Jl. Mayjen Sungkono, Dukuh Pakis, Surabaya, Jawa Timur</p></div><div><span class="font-label-md text-label-md text-on-surface block">Hub Sumatera (Medan &amp; Jambi)</span><p>Jl. Balai Kota No. 1, Medan &amp; Kota Jambi</p></div><div><span class="font-label-md text-label-md text-on-surface block">Hub Makassar (Indonesia Timur)</span><p>Kawasan Bisnis Boulevard, Panakkukang, Makassar</p></div></div></div></div><div class="pt-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm font-body-sm text-body-sm text-on-surface-variant"><p>© 2025 PT Biantara Gemilang Media. All rights reserved.</p><p class="font-label-sm text-label-sm text-on-surface-variant">ISO 9001:2015 Certified OOH Infrastructure &amp; Media Network</p></div></div></footer>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    if(btn && menu) {
      btn.addEventListener('click', function() {
        menu.classList.toggle('hidden');
        menu.classList.toggle('flex');
      });
    }
  });
</script>
</body></html>
HTML;

$newLayout = $headerHtml.$bodyHtml.$footerHtml;
file_put_contents($f, $newLayout);
echo "app.blade.php fixed successfully.\n";
