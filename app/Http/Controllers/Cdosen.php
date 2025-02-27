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
            'nidn_dosen' => 'required|unique:dosen,nidn_dosen,except,id',
            'nama_dosen' => 'required|unique:dosen,nama_dosen'
        ], [
            'nidn_dosen.unique' => 'Nidn sudah terdaftar',
            'nama_dosen.unique' => 'Nama dosen sudah terdaftar',
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
            'nidn_dosen' => 'required|unique:dosen,nidn_dosen,except,id',
            'nama_dosen' => 'required|unique:dosen,nama_dosen'
        ], [
            'nidn_dosen.unique' => 'Nidn sudah terdaftar',
            'nama_dosen.unique' => 'Nama dosen sudah terdaftar',
        ]);
        $dosen = Mdosen::findOrFail($id);
        $dosen->update([
            'nidn_dosen' => $request->nidn_dosen,
            'nama_dosen' => $request->nama_dosen
        ]);
        return redirect()->route('dosen.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil diubah', 'icon' => 'success']);
    }

    public function bulkDelete(Request $request)
    {
        if ($request->has('delete_all')) {
            // Cek apakah ada data dosen yang memiliki jadwal terkait
            $dosenDenganJadwal = Mdosen::whereHas('jadwal')->pluck('nama_dosen')->toArray();

            if (!empty($dosenDenganJadwal)) {
                return redirect()->back()->with('status', [
                    'judul' => 'Gagal',
                    'pesan' => 'Beberapa data dosen tidak dapat dihapus karena masih memiliki jadwal terkait: ' . implode(', ', $dosenDenganJadwal),
                    'icon' => 'error',
                ]);
            }

            Mdosen::truncate(); // Hapus semua data dosen jika tidak ada yang memiliki jadwal terkait
            return redirect()->back()->with('success', 'Semua data dosen berhasil dihapus!');
        }

        if ($request->has('selected')) {
            $ids = explode(',', $request->selected);
            $dosenList = Mdosen::whereIn('id', $ids)->get();
            $blockedDosen = [];

            foreach ($dosenList as $matkul) {
                if ($matkul->jadwal()->exists()) {
                    $blockedDosen[] = $matkul->nama_dosen; // Simpan nama matkul yang tidak bisa dihapus
                    continue; // Lewati penghapusan matkul ini
                }
                $matkul->delete();
            }

            if (!empty($blockedDosen)) {
                return redirect()->back()->with('status', [
                    'judul' => 'Gagal',
                    'pesan' => 'Beberapa data dosen tidak dapat dihapus karena masih memiliki jadwal terkait: ' . implode(', ', $blockedDosen),
                    'icon' => 'error',
                ]);
            }

            return redirect()->back()->with('success', 'data dosen yang dipilih berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy($id)
    // {
    //     $dosen = Mdosen::findOrFail($id);
    //     $dosen->delete();
    //     return redirect()->route('dosen.index')->with('status', ['judul' => 'Berhasil', 'pesan' => 'Data berhasil dihapus', 'icon' => 'success']);
    // }
}
