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
            'nama_ruangan' => 'required|min:1|max:30',
        ]);
        $ruangan = Mruangan::findOrFail($id);
        $ruangan->update([
            // 'id_ruangan' => $request->id_ruangan,
            'nama_ruangan' => $request->nama_ruangan,
        ]);
        return redirect()->route('ruangan.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil diubah', 'icon' => 'success']);
    }

    public function bulkDelete(Request $request)
     {
         if ($request->has('delete_all')) {
             // Cek apakah ada data ruangan yang memiliki jadwal terkait
             $ruanganDenganJadwal = Mruangan::whereHas('jadwal')->pluck('nama_ruangan')->toArray();
     
             if (!empty($ruanganDenganJadwal)) {
                 return redirect()->back()->with('status', [
                     'judul' => 'Gagal',
                     'pesan' => 'Beberapa data ruangan tidak dapat dihapus karena masih memiliki jadwal terkait: ' . implode(', ', $ruanganDenganJadwal),
                     'icon' => 'error',
                 ]);
             }
     
             Mruangan::truncate(); // Hapus semua data ruangan jika tidak ada yang memiliki jadwal terkait
             return redirect()->back()->with('success', 'Semua data ruangan berhasil dihapus!');
         }
     
         if ($request->has('selected')) {
             $ids = explode(',', $request->selected);
             $ruanganList = Mruangan::whereIn('id', $ids)->get();
             $blockedRuangan = [];
     
             foreach ($ruanganList as $ruangan) {
                 if ($ruangan->jadwal()->exists()) {
                     $blockedRuangan[] = $ruangan->nama_ruangan; // Simpan nama ruangan yang tidak bisa dihapus
                     continue; // Lewati penghapusan ruangan ini
                 }
                 $ruangan->delete();
             }
             
             if (!empty($blockedRuangan)) {
                 return redirect()->back()->with('status', [
                     'judul' => 'Gagal',
                     'pesan' => 'Beberapa data ruangan tidak dapat dihapus karena masih memiliki jadwal terkait: ' . implode(', ', $blockedRuangan),
                     'icon' => 'error',
                 ]);
             }
     
             return redirect()->back()->with('success', 'data ruangan yang dipilih berhasil dihapus!');
         }
     
         return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
     }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy($id)
    // {
    //     $ruangan = Mruangan::findOrFail($id);
    //     $ruangan->delete();
    //     return redirect()->route('ruangan.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil dihapus', 'icon' => 'success']);
    // }
}
