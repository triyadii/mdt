<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::latest()->paginate(10);
        return view('beritas.index', compact('beritas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaBerita' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'keteranganBerita' => 'nullable|string',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('gambar');
        $data['slugBerita'] = Str::slug($request->namaBerita);
        
        // Handle unique slug
        $originalSlug = $data['slugBerita'];
        $count = 1;
        while (Berita::where('slugBerita', $data['slugBerita'])->exists()) {
            $data['slugBerita'] = $originalSlug . '-' . $count;
            $count++;
        }

        $gambarPaths = [];
        if ($request->hasFile('gambar')) {
            $files = $request->file('gambar');
            // Limit to 3 files max
            $files = array_slice($files, 0, 3);
            
            foreach ($files as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/berita'), $filename);
                $gambarPaths[] = 'images/berita/' . $filename;
            }
        }
        $data['gambar'] = $gambarPaths;

        if (empty($data['author']) && auth()->check()) {
            $data['author'] = auth()->user()->nama ?? auth()->user()->username;
        }

        Berita::create($data);

        return redirect()->route('beritas.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'namaBerita' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'keteranganBerita' => 'nullable|string',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('gambar');
        
        // Update slug if title changed
        if ($request->namaBerita !== $berita->namaBerita) {
            $data['slugBerita'] = Str::slug($request->namaBerita);
            $originalSlug = $data['slugBerita'];
            $count = 1;
            while (Berita::where('slugBerita', $data['slugBerita'])->where('id', '!=', $berita->id)->exists()) {
                $data['slugBerita'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        $gambarPaths = $berita->gambar ?? [];
        if ($request->hasFile('gambar')) {
            $files = $request->file('gambar');
            // Limit new uploaded to fit inside 3 max total (or just replace up to 3)
            // Let's just replace all for simplicity or append. We will just overwrite here for simplicity.
            $files = array_slice($files, 0, 3);
            $gambarPaths = []; // reset images if new ones uploaded
            
            foreach ($files as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/berita'), $filename);
                $gambarPaths[] = 'images/berita/' . $filename;
            }
            $data['gambar'] = $gambarPaths;
        }

        $berita->update($data);

        return redirect()->route('beritas.index')
            ->with('success', 'Berita berhasil diubah.');
    }

    public function destroy(Berita $berita)
    {
        $berita->delete();

        return redirect()->route('beritas.index')
            ->with('success', 'Berita berhasil dihapus.');
    }
}
