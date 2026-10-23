<?php

$dir = '/var/www/html/WebsiteBiantaraGemilangMedia/resources/views';
$files = ['portfolio.blade.php', 'kontak.blade.php', 'katalogDetail.blade.php', 'tentangKami.blade.php'];

foreach ($files as $file) {
    $content = file_get_contents("$dir/$file");

    // If the file still has <!DOCTYPE html>, we need to strip it.
    if (strpos($content, '<!DOCTYPE html>') !== false) {
        $mainStart = strpos($content, '<main');
        if ($mainStart !== false) {
            $mainStart = strpos($content, '>', $mainStart) + 1;

            // Find </main>
            $mainEnd = strrpos($content, '</main>');

            if ($mainEnd !== false) {
                $bodyPart = substr($content, $mainStart, $mainEnd - $mainStart);
                $newContent = "@extends('layouts.app')\n\n@section('content')\n".trim($bodyPart)."\n@endsection\n";
                file_put_contents("$dir/$file", $newContent);
                echo "Fixed $file\n";
            }
        }
    } else {
        echo "$file is already using layout\n";
    }
}
