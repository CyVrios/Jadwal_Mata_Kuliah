<?php

namespace App\Http\Controllers;

use App\Models\Mruangan;
use Illuminate\Http\Request;

class Cruangan extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangan = Mruangan::get();
        return view('ruangan.index', compact('ruangan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ruangan.tambah');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validated =  $request->validate([
            // 'id_ruangan' => 'required|min:1|max:10',
            'nama_ruangan' => 'required|min:1|max:20',
        ]);
        $ruangan = Mruangan::create($validated);
        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Data berhasil ditambahkan!',
            'icon' => 'success'
        ];
        $last_data = [
            // 'id_ruangan' => $ruangan->id_ruangan,
            'nama_ruangan' => $ruangan->nama_ruangan,
        ];

        return redirect()->route('ruangan.index')->with(compact('status', 'last_data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Mruangan $mruangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mruangan $id)
    {
        $ruangan = Mruangan::findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            // 'id_ruangan' => 'required|min:1|max:10',
            'nama_ruangan' => 'required|min:1|max:20',
        ]);
        $ruangan = Mruangan::findOrFail($id);
        $ruangan->update([
            // 'id_ruangan' => $request->id_ruangan,
            'nama_ruangan' => $request->nama_ruangan,
        ]);
        return redirect()->route('ruangan.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil diubah', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ruangan = Mruangan::findOrFail($id);
        $ruangan->delete();
        return redirect()->route('ruangan.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil dihapus', 'icon' => 'success']);
    }
}
