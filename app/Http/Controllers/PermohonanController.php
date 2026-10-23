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
        $validated = $request->validate([
            'namaPermohonan' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:20',
            'jenisJasa' => 'nullable|string|max:255',
            'keteranganPermohonan' => 'nullable|string',
            'tanggal_konsultasi' => 'nullable|date',
            'jam_konsultasi' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        Permohonan::create($validated);

        return redirect()->route('permohonans.index')
            ->with('success', 'Permohonan berhasil ditambahkan.');
    }

    public function update(Request $request, Permohonan $permohonan)
    {
        $validated = $request->validate([
            'namaPermohonan' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:20',
            'jenisJasa' => 'nullable|string|max:255',
            'keteranganPermohonan' => 'nullable|string',
            'tanggal_konsultasi' => 'nullable|date',
            'jam_konsultasi' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        $permohonan->update($validated);

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
