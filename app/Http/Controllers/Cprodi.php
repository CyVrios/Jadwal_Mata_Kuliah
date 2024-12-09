<?php

namespace App\Http\Controllers;

use App\Models\Mprodi;
use Illuminate\Http\Request;

class Cprodi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prodi = Mprodi::get();
        return view('prodi.index', compact('prodi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('prodi.tambah');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validated = $request->validate([
            'id_prodi' => 'required|min:1|max:50',
            'nama_prodi' => 'required|min:1|max:50'
        ]);
        $prodi = Mprodi::create($validated);

        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Data berhasil ditambahkan!',
            'icon' => 'success'
        ];
        $last_data = [
            'id_prodi' => $prodi->id_prodi,
            'nama_prodi' => $prodi->nama_prodi,
        ];
        return redirect()->route('prodi.index')->with(compact('status', 'last_data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Mprodi $mdosen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mprodi $id)
    {
        $prodi = Mprodi::findOrFail($id);
        return view('prodi.edit', compact('prodi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_prodi' => 'required|min:1|max:50',
            'nama_prodi' => 'required|min:1|max:50'
        ]);
        $prodi = Mprodi::findOrFail($id);
        $prodi->update([
            'id_prodi' => $request->id_prodi,
            'nama_prodi' => $request->nama_prodi
        ]);
        return redirect()->route('prodi.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil diubah', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $prodi = Mprodi::findOrFail($id);
        $prodi->delete();
        return redirect()->route('prodi.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil dihapus', 'icon' => 'success']);
    }
}
