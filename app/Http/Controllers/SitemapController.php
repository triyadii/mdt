<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap for SEO crawlers.
     */
    public function index(): Response
    {
        $beritas = Berita::orderBy('updated_at', 'desc')->get();
        $latestBerita = $beritas->first();

        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => $latestBerita ? $latestBerita->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('front.berita'),
                'lastmod' => $latestBerita ? $latestBerita->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => route('front.portfolio'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
        ];

        foreach ($beritas as $berita) {
            $urls[] = [
                'loc' => route('front.berita.detail', $berita->slugBerita),
                'lastmod' => $berita->updated_at ? $berita->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}
