<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $about = About::first();
        return view('abouts.index', compact('about'));
    }

    public function store(Request $request)
    {
        if (About::count() > 0) {
            return redirect()->back()->with('error', 'Keterangan Tentang sudah ada, tidak bisa menambah lagi.');
        }

        $request->validate([
            'keterangan_tentang' => 'required|string',
        ]);

        About::create($request->all());

        return redirect()->route('abouts.index')->with('success', 'Keterangan Tentang berhasil ditambahkan.');
    }

    public function update(Request $request, $uuid)
    {
        $about = About::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'keterangan_tentang' => 'required|string',
        ]);

        $about->update($request->all());

        return redirect()->route('abouts.index')->with('success', 'Keterangan Tentang berhasil diupdate.');
    }
}
