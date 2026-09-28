<?php

namespace App\Http\Controllers;

use App\Models\Keunggulan;
use Illuminate\Http\Request;

class KeunggulanController extends Controller
{
    public function index()
    {
        $keunggulans = Keunggulan::latest()->paginate(10);
        return view('keunggulans.index', compact('keunggulans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaUnggulan' => 'required|string|max:255',
            'keteranganUnggulan' => 'nullable|string',
        ]);

        Keunggulan::create($request->all());

        return redirect()->route('keunggulans.index')
            ->with('success', 'Keunggulan berhasil ditambahkan.');
    }

    public function update(Request $request, Keunggulan $keunggulan)
    {
        $request->validate([
            'namaUnggulan' => 'required|string|max:255',
            'keteranganUnggulan' => 'nullable|string',
        ]);

        $keunggulan->update($request->all());

        return redirect()->route('keunggulans.index')
            ->with('success', 'Keunggulan berhasil diubah.');
    }

    public function destroy(Keunggulan $keunggulan)
    {
        $keunggulan->delete();

        return redirect()->route('keunggulans.index')
            ->with('success', 'Keunggulan berhasil dihapus.');
    }
}
