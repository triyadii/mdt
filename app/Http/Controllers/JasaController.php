<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use Illuminate\Http\Request;

class JasaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jasas = Jasa::latest()->paginate(10);

        return view('jasas.index', compact('jasas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jasas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'namaJasa' => 'required|string|max:255',
            'harga_mulai' => 'nullable|string|max:255',
            'estimasi_waktu' => 'nullable|string|max:255',
            'keteranganJasa' => 'nullable|string',
        ]);

        Jasa::create($validated);

        return redirect()->route('jasas.index')
            ->with('success', 'Jasa & harga project berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jasa $jasa)
    {
        return view('jasas.show', compact('jasa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jasa $jasa)
    {
        return view('jasas.edit', compact('jasa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jasa $jasa)
    {
        $validated = $request->validate([
            'namaJasa' => 'required|string|max:255',
            'harga_mulai' => 'nullable|string|max:255',
            'estimasi_waktu' => 'nullable|string|max:255',
            'keteranganJasa' => 'nullable|string',
        ]);

        $jasa->update($validated);

        return redirect()->route('jasas.index')
            ->with('success', 'Jasa & harga project berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jasa $jasa)
    {
        $jasa->delete();

        return redirect()->route('jasas.index')
            ->with('success', 'Jasa deleted successfully');
    }
}
