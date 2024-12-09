<?php

namespace App\Http\Controllers;

use App\Models\Mkelas;
use Illuminate\Http\Request;

class Ckelas extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Mkelas::get();
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelas.tambah');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validatedData = $request->validate([
            'id_kelas' => 'required|min:1|max:10',
            'nama_kelas' => 'required|min:1|max:10'
        ]);

        $kelas = Mkelas::create($validatedData);

        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Data berhasil ditambahkan!',
            'icon' => 'success'
        ];
        $last_data = [
            'id_dosen' => $kelas->id_kelas,
            'nama_dosen' => $kelas->nama_kelas
        ];


        return redirect()->route('kelas.index')->with(compact('status', 'last_data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Mkelas $mruangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mkelas $id)
    {
        $kelas = Mkelas::findOrFail($id);
        return view('kelas.edit', compact('kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_kelas' => 'required|min:1|max:10',
            'nama_kelas' => 'required|min:1|max:10'
        ]);
        $kelas = Mkelas::findOrFail($id);
        $kelas->update([
            'id_kelas' => $request->id_kelas,
            'nama_kelas' => $request->nama_kelas,
        ]);
        return redirect()->route('kelas.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil diubah', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kelas = Mkelas::findOrFail($id);
        $kelas->delete();
        return redirect()->route('kelas.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil dihapus', 'icon' => 'success']);
    }
}
