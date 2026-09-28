<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;

class ProjectTypeController extends Controller
{
    public function index()
    {
        $projectTypes = ProjectType::orderBy('created_at', 'desc')->get();
        return view('project_types.index', compact('projectTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:255',
        ]);

        ProjectType::create($request->all());

        return redirect()->route('project-types.index')->with('success', 'Jenis Project berhasil ditambahkan.');
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:255',
        ]);

        $projectType = ProjectType::where('uuid', $uuid)->firstOrFail();
        $projectType->update($request->all());

        return redirect()->route('project-types.index')->with('success', 'Jenis Project berhasil diupdate.');
    }

    public function destroy($uuid)
    {
        $projectType = ProjectType::where('uuid', $uuid)->firstOrFail();
        $projectType->delete();

        return redirect()->route('project-types.index')->with('success', 'Jenis Project berhasil dihapus.');
    }
}
