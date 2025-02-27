<?php

namespace App\Imports;

use App\Models\Mmatkul;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class MatkulImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {

        $errors = [];
        $matkulData = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Baris mulai dari 2 karena header

            $validator = Validator::make($row->toArray(), [
                'kode_matkul' => 'required|string|unique:matkul,kode_matkul',
                'nama_matkul' => 'required|string',
                'sks' => 'required|integer',
                'smt' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $errors[$rowNumber] = $validator->errors()->all();
            } else {
                // Simpan langsung ke database
                Mmatkul::create([
                    'kode_matkul' => $row['kode_matkul'],
                    'nama_matkul' => $row['nama_matkul'],
                    'sks' => $row['sks'],
                    'smt' => $row['smt'],
                ]);
            }
        }

        if (!empty($errors)) {
            session()->put('matkul_import_errors', $errors);
        }
    }
}
