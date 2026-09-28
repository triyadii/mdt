<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('created_at', 'desc')->get();
        return view('partners.index', compact('partners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
        ]);

        Partner::create($request->all());

        return redirect()->route('partners.index')->with('success', 'Perusahaan/Mitra berhasil ditambahkan.');
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
        ]);

        $partner = Partner::where('uuid', $uuid)->firstOrFail();
        $partner->update($request->all());

        return redirect()->route('partners.index')->with('success', 'Perusahaan/Mitra berhasil diupdate.');
    }

    public function destroy($uuid)
    {
        $partner = Partner::where('uuid', $uuid)->firstOrFail();
        $partner->delete();

        return redirect()->route('partners.index')->with('success', 'Perusahaan/Mitra berhasil dihapus.');
    }
}
