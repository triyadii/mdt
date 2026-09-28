<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
        return view('testimonials.index', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_testimoni' => 'required|string|max:255',
            'keterangan_testimoni' => 'required|string',
        ]);

        Testimonial::create($request->all());

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_testimoni' => 'required|string|max:255',
            'keterangan_testimoni' => 'required|string',
        ]);

        $testimonial = Testimonial::where('uuid', $uuid)->firstOrFail();
        $testimonial->update($request->all());

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil diupdate.');
    }

    public function destroy($uuid)
    {
        $testimonial = Testimonial::where('uuid', $uuid)->firstOrFail();
        $testimonial->delete();

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil dihapus.');
    }
}
