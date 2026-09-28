<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::with('type')->orderBy('created_at', 'desc')->get();
        $projectTypes = \App\Models\ProjectType::orderBy('nama_jenis', 'asc')->get();
        return view('portfolios.index', compact('portfolios', 'projectTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_project' => 'required|string|max:255',
            'jenis_project' => 'required|string|max:255',
            'keterangan_project' => 'required|string',
            'link_project' => 'nullable|string|max:255',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gambar' => 'nullable|array|max:3',
        ]);

        $data = $request->except('gambar');
        $gambarPaths = [];

        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $path = $image->store('portfolios', 'public');
                $gambarPaths[] = $path;
            }
        }

        $data['gambar'] = $gambarPaths;

        Portfolio::create($data);

        return redirect()->route('portfolios.index')->with('success', 'Portofolio berhasil ditambahkan.');
    }

    public function update(Request $request, $uuid)
    {
        $portfolio = Portfolio::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'nama_project' => 'required|string|max:255',
            'jenis_project' => 'required|string|max:255',
            'keterangan_project' => 'required|string',
            'link_project' => 'nullable|string|max:255',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gambar' => 'nullable|array|max:3',
            'hapus_gambar' => 'nullable|array',
        ]);

        $data = $request->except(['gambar', 'hapus_gambar']);
        $gambarPaths = $portfolio->gambar ?? [];

        // Handle deleted images
        if ($request->has('hapus_gambar')) {
            foreach ($request->hapus_gambar as $hapusImg) {
                if (($key = array_search($hapusImg, $gambarPaths)) !== false) {
                    Storage::disk('public')->delete($hapusImg);
                    unset($gambarPaths[$key]);
                }
            }
            $gambarPaths = array_values($gambarPaths); // reindex
        }

        // Check total images constraint
        $uploadedCount = $request->hasFile('gambar') ? count($request->file('gambar')) : 0;
        if (count($gambarPaths) + $uploadedCount > 3) {
            return redirect()->back()->with('error', 'Maksimal 3 gambar yang diperbolehkan.');
        }

        // Handle new images
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $path = $image->store('portfolios', 'public');
                $gambarPaths[] = $path;
            }
        }

        $data['gambar'] = $gambarPaths;

        $portfolio->update($data);

        return redirect()->route('portfolios.index')->with('success', 'Portofolio berhasil diupdate.');
    }

    public function destroy($uuid)
    {
        $portfolio = Portfolio::where('uuid', $uuid)->firstOrFail();
        
        if (!empty($portfolio->gambar)) {
            foreach ($portfolio->gambar as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $portfolio->delete();

        return redirect()->route('portfolios.index')->with('success', 'Portofolio berhasil dihapus.');
    }
}
