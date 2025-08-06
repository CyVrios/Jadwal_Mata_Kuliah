<?php

namespace App\Exports;

use App\Models\Mjadwal;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JadwalExport implements FromCollection, WithHeadings
{
    protected $smt;
    protected $prodi;

    // Constructor untuk menerima filter smt dan prodi
    public function __construct($smt = null, $prodi = null)
    {
        $this->smt = $smt;
        $this->prodi = $prodi;
    }

    /**
     * Mengambil data yang akan diekspor
     */
    public function collection()
{
    $query = Mjadwal::with(['prodi', 'matkul', 'dosen', 'ruangan']);

    // Filter berdasarkan smt dari relasi matkul
    if (!empty($this->smt)) {
        $query->whereHas('matkul', function ($q) {
            $q->where('smt', $this->smt);
        });
    }

    // Filter berdasarkan prodi
    if (!empty($this->prodi)) {
        $query->where('prodi_id', $this->prodi);
    }

    $jadwal = $query
        ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
        ->orderBy('jam_mulai', 'asc')
        ->get();

    return $jadwal->map(function ($jadwal) {
        return [
            'hari' => $jadwal->hari ?? '-',
            'jam_mulai' => $jadwal->jam_mulai ?? '-',
            'jam_selesai' => $jadwal->jam_selesai ?? '-',
            'kode_matkul' => $jadwal->matkul->kode_matkul ?? '-',
            'nama_matkul' => $jadwal->matkul->nama_matkul ?? '-',
            'smt' => $jadwal->matkul->smt ?? '-',
            'sks' => $jadwal->matkul->sks ?? '-',
            'nama_dosen' => $jadwal->dosen->nama_dosen ?? '-',
            'kelas' => $jadwal->kelas ?? '-',
            'nama_ruangan' => $jadwal->ruangan->nama_ruangan ?? '-',
            'prodi' => $jadwal->prodi->nama_prodi ?? '-',
        ];
    });

}

    /**
     * Mendefinisikan heading untuk file Excel
     */
    public function headings(): array
    {
        return [
            'Hari',
            'Jam Mulai',
            'Jam Selesai',
            'Kode Matkul',
            'Nama Matkul',
            'Semester',
            'SKS',
            'Nama Dosen',
            'Kelas',
            'Nama Ruangan',
            'Prodi',
        ];
    }
}
