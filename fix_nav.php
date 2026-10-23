<?php

$f = '/var/www/html/WebsiteBiantaraGemilangMedia/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Find the <nav> block
$navStart = strpos($c, '<nav class="hidden xl:flex');
$navEnd = strpos($c, '</nav>', $navStart) + 6;

$navHtml = <<<'HTML'
<nav class="hidden xl:flex items-center gap-space-xs" data-active-classes="bg-surface-container text-on-surface font-label-lg text-label-lg rounded-lg">
  <a {!! request()->is('/') ? 'aria-current="page" class="px-space-md py-space-sm transition-colors bg-surface-container text-on-surface font-label-lg text-label-lg rounded-lg"' : 'class="font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-space-md py-space-sm rounded-lg transition-colors"' !!} href="{{ url('/') }}">Beranda</a>
  <a {!! request()->is('Katalog*') ? 'aria-current="page" class="px-space-md py-space-sm transition-colors bg-surface-container text-on-surface font-label-lg text-label-lg rounded-lg"' : 'class="font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-space-md py-space-sm rounded-lg transition-colors"' !!} href="{{ url('/Katalog') }}">Katalog Lokasi OOH</a>
  <a {!! request()->is('TentangKami') ? 'aria-current="page" class="px-space-md py-space-sm transition-colors bg-surface-container text-on-surface font-label-lg text-label-lg rounded-lg"' : 'class="font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-space-md py-space-sm rounded-lg transition-colors"' !!} href="{{ url('/TentangKami') }}">Tentang Kami</a>
  <a {!! request()->is('Portfolio') ? 'aria-current="page" class="px-space-md py-space-sm transition-colors bg-surface-container text-on-surface font-label-lg text-label-lg rounded-lg"' : 'class="font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-space-md py-space-sm rounded-lg transition-colors"' !!} href="{{ url('/Portfolio') }}">Layanan &amp; Portofolio</a>
  <a {!! request()->is('Kontak') ? 'aria-current="page" class="px-space-md py-space-sm transition-colors bg-surface-container text-on-surface font-label-lg text-label-lg rounded-lg"' : 'class="font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-space-md py-space-sm rounded-lg transition-colors"' !!} href="{{ url('/Kontak') }}">Kontak</a>
</nav>
HTML;

$c = substr_replace($c, $navHtml, $navStart, $navEnd - $navStart);
file_put_contents($f, $c);
echo "Nav updated successfully.\n";
