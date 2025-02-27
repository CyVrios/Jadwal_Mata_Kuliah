<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MatkulImport;
use App\Models\Mmatkul;

class Cimport extends Controller
{

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xls,xlsx'
    ]);

    try {
        Excel::import(new MatkulImport, $request->file('file'));
        return redirect()->back()->with('success', 'Data berhasil diimport!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
    }
}


    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        // Import data ke session untuk preview
        Excel::import(new MatkulImport, $request->file('file'));

        return redirect()->route('matkul.import.preview');
    }

    public function showPreview()
    {
        $matkulData = session('matkul_import_preview', []);
        $errors = session('matkul_import_errors', []);

        return view('matkul.import-preview', compact('matkulData', 'errors'));
    }

    public function confirmImport()
    {
        $matkulData = session('matkul_import_preview', []);

        if (empty($matkulData)) {
            return redirect()->route('matkul.index')->with('error', 'Tidak ada data yang diimport.');
        }

        // Simpan data ke database
        Mmatkul::insert($matkulData);

        // Hapus session
        session()->forget(['matkul_import_preview', 'matkul_import_errors']);

        return redirect()->route('matkul.index')->with('success', 'Data berhasil diimport!');
    }
}
