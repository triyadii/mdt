<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use App\Models\About;
use App\Models\Keunggulan;
use App\Models\Partner;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\Jasa;
use App\Models\Portfolio;
use App\Models\Berita;

class FrontController extends Controller
{
    public function index()
    {
        $company = CompanyProfile::first();
        $about = About::first();
        $keunggulans = Keunggulan::orderBy('created_at', 'asc')->get();
        $partners = Partner::orderBy('created_at', 'desc')->get();
        $faqs = Faq::orderBy('created_at', 'asc')->get();
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
        $jasas = Jasa::orderBy('created_at', 'asc')->get();
        $portfolios = Portfolio::with('type')->orderBy('created_at', 'desc')->take(5)->get();
        $projectTypes = \App\Models\ProjectType::orderBy('created_at', 'asc')->get();
        
        // 5 Berita teratas
        $beritas = Berita::orderBy('created_at', 'desc')->take(5)->get();

        return view('welcome', compact(
            'company', 'about', 'keunggulans', 'partners', 
            'faqs', 'testimonials', 'jasas', 'portfolios', 'projectTypes', 'beritas'
        ));
    }

    public function berita()
    {
        $company = CompanyProfile::first();
        $beritas = Berita::orderBy('created_at', 'desc')->paginate(12);
        return view('berita.index', compact('company', 'beritas'));
    }

    public function portfolio()
    {
        $company = CompanyProfile::first();
        $portfolios = Portfolio::with('type')->orderBy('created_at', 'desc')->get();
        $projectTypes = \App\Models\ProjectType::orderBy('created_at', 'asc')->get();
        return view('portfolio.index', compact('company', 'portfolios', 'projectTypes'));
    }

    public function beritaDetail($slug)
    {
        $company = CompanyProfile::first();
        $berita = Berita::where('slugBerita', $slug)->firstOrFail();
        
        // Berita lainnya
        $otherBeritas = Berita::where('slugBerita', '!=', $slug)
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();

        return view('berita.detail', compact('company', 'berita', 'otherBeritas'));
    }
}
