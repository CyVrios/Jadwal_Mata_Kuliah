<?php

namespace App\Http\Controllers;

use App\Models\Mmatkul;
// use App\Models\Mprodi;
use Illuminate\Http\Request;

class Cmatkul extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $matkul  = Mmatkul::with(['prodi'])->get();

        $matkul = Mmatkul::all();
        // $prodi = Mprodi::all();
        return view('matkul.index', compact('matkul'));
        // return view('matkul.index', compact('matkul', 'prodi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $prodi = Mprodi::all();
        // return view('matkul.create', compact('prodi'));
        return view('matkul.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_matkul' => 'required|unique:matkul,kode_matkul,except,id',
            'nama_matkul' => 'required',
            'smt' => 'required',
            'sks' => 'required',
        ],[
            'kode_matkul.unique' => 'Kode mata kuliah sudah terdaftar'
        ]);

        $matkul = Mmatkul::create($validated);

        // $matkul->load('prodi');

        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Data berhasil ditambahkan!',
            'icon' => 'success'
        ];
        $last_data = [
            'kode_matkul' => $matkul->kode_matkul,
            'nama_matkul' => $matkul->nama_matkul,
            'smt'         => $matkul->smt,
            'sks'         => $matkul->sks,
        ];
        return redirect()->route('matkul.index')->with(compact('status', 'last_data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Mmatkul $mruangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mmatkul $id)
    {
        $matkul = Mmatkul::findOrFail($id);

        // $prodi = Mprodi::all();
        // return view('matkul.edit', compact('matkul', 'prodi'));
        return view('matkul.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $matkul = Mmatkul::findOrFail($id);

        $validated = $request->validate([
            'kode_matkul' => 'required|unique:matkul,kode_matkul,except,id',
            'nama_matkul' => 'required',
            'smt' => 'required',
            'sks' => 'required',
        ],[
            'kode_matkul.unique' => 'Kode mata kuliah sudah terdaftar'
        ]);

        $matkul->update($validated);
        return redirect()->route('matkul.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil diubah', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */

     public function bulkDelete(Request $request)
{
    if ($request->has('delete_all')) {
        // Hapus hanya data yang tidak memiliki jadwal terkait
        $deleted = Mmatkul::whereDoesntHave('jadwal')->delete();

        if ($deleted > 0) {
            return redirect()->back()->with('success', 'Semua mata kuliah tanpa jadwal berhasil dihapus!');
        }

        return redirect()->back()->with('status', [
            'judul' => 'Gagal',
            'pesan' => 'Tidak ada mata kuliah yang dapat dihapus karena masih memiliki jadwal terkait.',
            'icon' => 'error',
        ]);
    }

    // Jika ada pilihan spesifik
    if ($request->has('selected')) {
        $ids = explode(',', $request->selected);

        // Cek mana saja yang bisa dihapus
        $deleted = Mmatkul::whereIn('id', $ids)->whereDoesntHave('jadwal')->delete();

        if ($deleted > 0) {
            return redirect()->back()->with('success', 'Mata kuliah yang dipilih berhasil dihapus!');
        }

        return redirect()->back()->with('status', [
            'judul' => 'Gagal',
            'pesan' => 'Mata kuliah yang dipilih masih memiliki jadwal terkait dan tidak dapat dihapus.',
            'icon' => 'error',
        ]);
    }

    return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
}

     


    // public function destroy($id)
    // {
    //     $matkul = Mmatkul::findOrFail($id);

    //     // Pastikan jadwal yang terkait dengan mata kuliah ini sudah dihapus
    //     if ($matkul->jadwal()->exists()) {
    //         return redirect()->route('matkul.index')->with('status', [
    //             'judul' => 'Gagal',
    //             'pesan' => 'Mata kuliah tidak dapat dihapus karena masih memiliki jadwal terkait.',
    //             'icon' => 'error',
    //         ]);
    //     }

    //     $matkul->delete();
    //     return redirect()->route('matkul.index')->with('status', [
    //         'judul' => 'Berhasil',
    //         'pesan' => 'Data berhasil dihapus.',
    //         'icon' => 'success',
    //     ]);
    // }
}
