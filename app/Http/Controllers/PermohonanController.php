<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    public function index()
    {
        $permohonans = Permohonan::latest()->paginate(10);
        return view('permohonans.index', compact('permohonans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaPermohonan' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:20',
            'jenisJasa' => 'nullable|string|max:255',
            'keteranganPermohonan' => 'nullable|string',
        ]);

        Permohonan::create($request->all());

        return redirect()->route('permohonans.index')
            ->with('success', 'Permohonan berhasil ditambahkan.');
    }

    public function update(Request $request, Permohonan $permohonan)
    {
        $request->validate([
            'namaPermohonan' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:20',
            'jenisJasa' => 'nullable|string|max:255',
            'keteranganPermohonan' => 'nullable|string',
        ]);

        $permohonan->update($request->all());

        return redirect()->route('permohonans.index')
            ->with('success', 'Permohonan berhasil diubah.');
    }

    public function destroy(Permohonan $permohonan)
    {
        $permohonan->delete();

        return redirect()->route('permohonans.index')
            ->with('success', 'Permohonan berhasil dihapus.');
    }
}
