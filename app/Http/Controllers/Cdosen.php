<?php

namespace App\Http\Controllers;

use App\Models\Mdosen;
use Illuminate\Http\Request;

class Cdosen extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dosen = Mdosen::get();
        return view('dosen.index', compact('dosen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dosen.tambah');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nidn_dosen' => 'required',
            'nama_dosen' => 'required'
        ]);

        $dosen = Mdosen::create($validatedData);

        // Debugging data yang akan dikirim ke session
        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Data berhasil ditambahkan!',
            'icon' => 'success'
        ];
        $last_data = [
            'nidn_dosen' => $dosen->nidn_dosen,
            'nama_dosen' => $dosen->nama_dosen
        ];

        return redirect()->route('dosen.index')->with(compact('status', 'last_data'));
    }




    /**
     * Display the specified resource.
     */
    public function show(Mdosen $mdosen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mdosen $id)
    {
        $dosen = Mdosen::findOrFail($id);
        return view('dosen.edit', compact('dosen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nidn_dosen' => 'required|min:1|max:50',
            'nama_dosen' => 'required|min:1|max:50'
        ]);
        $dosen = Mdosen::findOrFail($id);
        $dosen->update([
            'nidn_dosen' => $request->nidn_dosen,
            'nama_dosen' => $request->nama_dosen
        ]);
        return redirect()->route('dosen.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil diubah', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dosen = Mdosen::findOrFail($id);
        $dosen->delete();
        return redirect()->route('dosen.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil dihapus', 'icon' => 'success']);
    }
}
