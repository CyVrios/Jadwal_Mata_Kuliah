<?php

namespace App\Exports;

use App\Models\Mjadwal;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JadwalExport implements FromCollection, WithHeadings
{
    protected $smt;

    // Constructor untuk menerima filter smt
    public function __construct($smt = null)
    {
        $this->smt = $smt;
    }

    /**
     * Mengambil data yang akan diekspor
     */
    public function collection()
    {
        $query = Mjadwal::with(['matkul', 'dosen', 'ruangan']);

        // Filter berdasarkan smt dari relasi matkul
        if (!empty($this->smt)) {
            $query->whereHas('matkul', function ($q) {
                $q->where('smt', $this->smt);
            });
        }

        $jadwal = $query->get();

        // Debug jumlah data yang difilter
        Log::info("Jumlah data untuk smt '{$this->smt}': " . $jadwal->count());

        return $jadwal->map(function ($jadwal) {
            return [
                'hari' => $jadwal->hari,
                'jam_mulai' => $jadwal->jam_mulai,
                'jam_selesai' => $jadwal->jam_selesai,
                'kode_matkul' => $jadwal->matkul->kode_matkul ?? '-',
                'nama_matkul' => $jadwal->matkul->nama_matkul ?? '-',
                'smt' => $jadwal->matkul->smt ?? '-', // Ambil smt dari relasi matkul
                'sks' => $jadwal->sks,
                'nama_dosen' => $jadwal->dosen->nama_dosen ?? '-',
                'kelas' => $jadwal->kelas ?? '-',
                'nama_ruangan' => $jadwal->ruangan->nama_ruangan ?? '-',
                'mode_pembelajaran' => ucfirst($jadwal->mode_pembelajaran),
            ];
        });
    }

    /**
     * Tambahkan heading untuk kolom Excel
     */
    public function headings(): array
    {
        return [
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Kode Mata Kuliah',
            'Nama Mata Kuliah',
            'Semester', // Heading untuk kolom smt
            'SKS',
            'Dosen Pengampu',
            'Kelas',
            'Ruangan',
            'Mode Pembelajaran',
        ];
    }
}
