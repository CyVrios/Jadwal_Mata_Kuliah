<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Mjadwal;
use App\Models\Mdosen;
use App\Models\Mkelas;
use App\Models\Mruangan;
use App\Models\Mmatkul;
use App\Models\Mprodi;
use App\Exports\JadwalExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class Cjadwal extends Controller
{
    /**
     * Menampilkan daftar jadwal
     */
    public function index(Request $request)
    {
        // Mengambil data semester dari tabel matkul, tetapi hanya jika ada data di tabel jadwal
        $semesters = Mmatkul::whereHas('jadwal') // Pastikan hanya semester dengan jadwal
            ->select('smt')
            ->distinct()
            ->pluck('smt');

        // Mengambil smt dari request untuk filter
        $smt = $request->get('smt');

        // Mengambil data jadwal dengan filter smt (jika ada)
        $jadwal = Mjadwal::with(['dosen', 'ruangan', 'matkul', 'prodi'])
            ->when($smt, function ($query, $smt) {
                $query->whereHas('matkul', function ($q) use ($smt) {
                    $q->where('smt', $smt);
                });
            })
            ->get();

        // Data lain untuk dropdown
        $matkul = Mmatkul::all();
        $dosen = Mdosen::all();
        $ruangan = Mruangan::all();
        $prodi = Mprodi::all();

        // Mengirim semua data ke view
        return view('jadwal.index', compact('jadwal', 'matkul', 'dosen', 'ruangan', 'prodi', 'semesters'));
    }

    public function checkAvailableRooms(Request $request)
    {
        $hari = $request->input('hari');
        $jamMulai = $request->input('jam_mulai');
        $jamSelesai = $request->input('jam_selesai');

        if (!$hari || !$jamMulai || !$jamSelesai) {
            return response()->json(['error' => 'Parameter tidak lengkap'], 400);
        }

        $occupiedRooms = Mjadwal::where('hari', $hari)
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                    ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                    ->orWhereRaw('? BETWEEN jam_mulai AND jam_selesai', [$jamMulai])
                    ->orWhereRaw('? BETWEEN jam_mulai AND jam_selesai', [$jamSelesai]);
            })
            ->pluck('id_ruangan');

        $availableRooms = Mruangan::whereNotIn('id', $occupiedRooms)->get();

        return response()->json($availableRooms);
    }



    public function export(Request $request)
    {
        // Ambil parameter smt
        $smt = $request->get('smt');

        // Debug untuk memastikan smt diteruskan
        Log::info("Parameter smt diterima: " . ($smt ?? 'semua'));

        // Unduh file Excel dengan filter yang diterapkan
        return Excel::download(new JadwalExport($smt), 'jadwal.xlsx');
    }

    /**
     * Menampilkan form untuk membuat jadwal baru
     */
    public function create()
    {
        // Mengambil data dari tabel terkait untuk dropdown
        $dosen = Mdosen::all();
        $kelas = Mkelas::all();
        $ruangan = Mruangan::all();
        $matkul = Mmatkul::all();
        $prodi = Mprodi::all();

        return view('jadwal.create', compact('dosen', 'ruangan', 'matkul', 'prodi'));
    }

    /**
     * Menyimpan jadwal baru ke dalam database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kelas' => 'required',
            'id_dosen' => 'required|exists:dosen,id',
            'id_ruangan' => 'nullable|exists:ruangan,id',
            'id_prodi' => 'required|exists:prodi,id',
            'kode_matkul' => 'required|exists:matkul,id', // Gunakan kode matkul
            'sks' => 'required',
            'mode_pembelajaran' => 'required|in:luring,daring,luring/daring',
        ]);

        // Simpan jadwal ke database
        $jadwal = Mjadwal::create($validated);

        // Data terakhir untuk localStorage
        $last_data = [
            'hari' => $jadwal->hari,
            'jam_mulai' => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
            'kode_matkul' => $jadwal->matkul->kode_matkul ?? '-',
            'nama_matkul' => $jadwal->matkul->nama_matkul ?? '-',
            'nama_prodi' => $jadwal->prodi->nama_prodi ?? '-',
            'nama_dosen' => $jadwal->dosen->nama_dosen ?? '-',
            'kelas' => $jadwal->kelas,
            'nama_ruangan' => $jadwal->ruangan->nama_ruangan ?? '-',
            'smt' => $jadwal->matkul->smt ?? '-', // Ambil smt dari relasi matkul
            'sks' => $jadwal->sks,
            'mode_pembelajaran' => $jadwal->mode_pembelajaran,
        ];

        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Jadwal berhasil ditambahkan!',
            'icon' => 'success'
        ];

        return redirect()->route('jadwal.index')->with(compact('status', 'last_data'));
    }



    /**
     * Menampilkan detail jadwal tertentu
     */
    public function show($id)
    {
        $jadwal = Mjadwal::with(['dosen', 'ruangan', 'matkul'])->findOrFail($id);

        return view('jadwal.show', compact('jadwal'));
    }

    /**
     * Menampilkan form untuk mengedit jadwal
     */
    public function edit($id)
    {
        $jadwal = Mjadwal::findOrFail($id);

        // Data untuk dropdown
        $dosen = Mdosen::all();
        // $kelas = Mkelas::all();
        $ruangan = Mruangan::all();
        $matkul = Mmatkul::all();
        $prodi = Mprodi::all();

        return view('jadwal.edit', compact('jadwal', 'dosen', 'ruangan', 'matkul', 'prodi'));
    }

    /**
     * Memperbarui data jadwal di database
     */
    public function update(Request $request, $id)
    {
        $jadwal = Mjadwal::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'id_dosen' => 'required|exists:dosen,id',
            'kelas' => 'required',
            'id_ruangan' => 'nullable|exists:ruangan,id',
            'id_prodi' => 'required|exists:prodi,id',
            'kode_matkul' => 'required|exists:matkul,id',
            'sks' => 'required',
            'mode_pembelajaran' => 'required|in:luring,daring,luring/daring',
        ]);


        // Update data
        $jadwal->update($validated);

        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Jadwal berhasil ditambahkan!',
            'icon' => 'success'
        ];

        return redirect()->route('jadwal.index')->with(compact('status'));
    }

    /**
     * Menghapus jadwal tertentu dari database
     */
    public function destroy($id)
    {
        $jadwal = Mjadwal::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
