<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $profile = CompanyProfile::first();
        return view('company_profiles.index', compact('profile'));
    }

    public function store(Request $request)
    {
        if (CompanyProfile::count() > 0) {
            return redirect()->back()->with('error', 'Profil sudah ada, tidak bisa menambah lagi.');
        }

        $validated = $request->validate([
            'nama_profil' => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'thread' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'nomor_telepon' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
        ]);

        CompanyProfile::create($validated);

        return redirect()->route('company-profiles.index')->with('success', 'Profil berhasil ditambahkan.');
    }

    public function update(Request $request, $uuid)
    {
        $profile = CompanyProfile::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'nama_profil' => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'thread' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'nomor_telepon' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
        ]);

        $profile->update($validated);

        return redirect()->route('company-profiles.index')->with('success', 'Profil berhasil diupdate.');
    }
}
