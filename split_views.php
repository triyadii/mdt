<?php

$dir = '/var/www/html/WebsiteBiantaraGemilangMedia/resources/views';
$files = ['welcome.blade.php', 'katalog.blade.php', 'katalogDetail.blade.php', 'tentangKami.blade.php'];

// We will read welcome.blade.php to extract the header and footer
$welcome = file_get_contents("$dir/welcome.blade.php");

$headerEnd = strpos($welcome, '</header>') + 9;
$headerPart = substr($welcome, 0, $headerEnd);

// Fix navigation links in header
$headerPart = preg_replace('/href="#"\s+data-path="beranda"/', 'href="{{ url(\'/\') }}"', $headerPart);
$headerPart = preg_replace('/href="#"\s+data-path="katalog-lokasi-ooh"/', 'href="{{ url(\'/Katalog\') }}"', $headerPart);
$headerPart = preg_replace('/href="#"\s+data-path="tentang-kami"/', 'href="{{ url(\'/TentangKami\') }}"', $headerPart);

// For footer, find <footer
$footerStart = strpos($welcome, '<footer');
$footerPart = substr($welcome, $footerStart);

$layout = $headerPart."\n".'<main class="w-full pt-20 bg-surface min-h-screen">'."\n".'@yield(\'content\')'."\n".'</main>'."\n".$footerPart;

file_put_contents("$dir/layouts/app.blade.php", $layout);

foreach ($files as $file) {
    $content = file_get_contents("$dir/$file");

    // Find <main ...> and extract inside it.
    // Some might have <main class="w-full pt-20 bg-surface min-h-screen"><div class="flex flex-col w-full">
    $mainStart = strpos($content, '<main');
    $mainStart = strpos($content, '>', $mainStart) + 1;

    $mainEnd = strrpos($content, '</main>');

    $bodyPart = substr($content, $mainStart, $mainEnd - $mainStart);

    if ($file === 'katalog.blade.php') {
        // Change detail links to point to KatalogDetail
        $bodyPart = preg_replace('/onclick="openModal\([^)]+\)"/', 'onclick="window.location.href=\'{{ url(\'/KatalogDetail\') }}\'"', $bodyPart);
        // Also look for any 'href="#" data-path="katalog-lokasi-ooh"' inside to just point to KatalogDetail
    }

    $newContent = "@extends('layouts.app')\n\n@section('content')\n".trim($bodyPart)."\n@endsection\n";
    file_put_contents("$dir/$file", $newContent);
}

echo "Done\n";
