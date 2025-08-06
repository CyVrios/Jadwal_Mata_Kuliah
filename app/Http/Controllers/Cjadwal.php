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
        $jadwal = Mjadwal::with(['prodi', 'matkul', 'dosen', 'ruangan'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Mengambil data semester dari tabel matkul, tetapi hanya jika ada data di tabel jadwal
        $semesters = Mmatkul::whereHas('jadwal') // Pastikan hanya semester dengan jadwal
            ->select('smt')
            ->distinct()
            ->pluck('smt');

        // Mengambil data program studi dari tabel prodi
        $prodiList = Mprodi::select('id', 'nama_prodi')->distinct()->pluck('nama_prodi', 'id'); // Untuk dropdown

        // Mengambil smt dan prodi dari request untuk filter
        $smt = $request->get('smt');
        $prodi = $request->get('prodi');

        // Mengambil data jadwal dengan filter smt dan prodi (jika ada)
        $jadwal = Mjadwal::with(['dosen', 'ruangan', 'matkul', 'prodi'])
            ->when($smt, function ($query, $smt) {
                $query->whereHas('matkul', function ($q) use ($smt) {
                    $q->where('smt', $smt);
                });
            })
            ->when($prodi, function ($query, $prodi) {
                $query->where('prodi_id', $prodi);  // Filter berdasarkan id_prodi
            })
            ->get();

        // Data lain untuk dropdown
        $matkul = Mmatkul::all();
        $dosen = Mdosen::all();
        $ruangan = Mruangan::all();
        $prodi = Mprodi::all();

        // Mengirim semua data ke view
        return view('jadwal.index', compact('jadwal', 'matkul', 'dosen', 'ruangan', 'prodiList', 'prodi', 'semesters'));
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
                $query->where('jam_mulai', '<', $jamSelesai) // Perubahan di sini
                    ->where('jam_selesai', '>', $jamMulai); // Perubahan di sini
            })
            ->pluck('ruangan_id');

        $availableRooms = Mruangan::whereNotIn('id', $occupiedRooms)->get();

        return response()->json($availableRooms);
    }


    public function export(Request $request)
    {
        $smt = $request->input('smt');
        $prodi = $request->input('prodi');

        return Excel::download(new JadwalExport($smt, $prodi), 'jadwal_matkul.xlsx');
    }

    /**
     * Menampilkan form untuk membuat jadwal baru
     */
    public function create()
    {
        //
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
            'dosen_id' => 'required|exists:dosen,id',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'prodi_id' => 'required|exists:prodi,id',
            'kode_matkul' => 'required|exists:matkul,id',
        ]);

        // Cek apakah dosen sudah memiliki jadwal bertabrakan
        $cekJadwalDosen = Mjadwal::where('hari', $request->hari)
            ->where('dosen_id', $request->dosen_id)
            ->where(function ($query) use ($request) {
                $query->where('jam_mulai', '<', $request->jam_selesai) // Dosen sudah mengajar sebelum waktu selesai
                    ->where('jam_selesai', '>', $request->jam_mulai); // Dosen masih mengajar saat waktu mulai
            })
            ->exists();

        if ($cekJadwalDosen) {
            return redirect()->back()->withErrors(['dosen_id' => 'Dosen sudah memiliki jadwal pada waktu tersebut.'])->withInput();
        }

        // Cek tabrakan jadwal untuk kombinasi kelas + semester (dari kode_matkul)
        $semester = \App\Models\Mmatkul::find($request->kode_matkul)?->smt; // Ambil semester dari matkul

        $cekJadwalKelasSemester = Mjadwal::where('hari', $request->hari)
            ->where('kelas', $request->kelas)
            ->whereHas('matkul', function ($query) use ($semester) {
                $query->where('smt', $semester);
            })
            ->where(function ($query) use ($request) {
                $query->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($cekJadwalKelasSemester) {
            return redirect()->back()->withErrors(['kelas' => 'Kelas dengan semester tersebut sudah memiliki jadwal pada waktu ini.'])->withInput();
        }

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
            'sks' => $jadwal->matkul->sks ?? '-', // Ambil sks dari relasi matkul

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

        // Mendapatkan data ruangan yang tersedia berdasarkan jadwal yang telah ada
        $ruanganTersedia = Mruangan::all(); // Ambil semua ruangan yang ada
        $ruanganTersedia = $ruanganTersedia->filter(function ($ruangan) use ($jadwal) {
            $occupiedRooms = Mjadwal::where('hari', $jadwal->hari)
                ->where(function ($query) use ($jadwal) {
                    $query->whereBetween('jam_mulai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                        ->orWhereBetween('jam_selesai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                        ->orWhereRaw('? BETWEEN jam_mulai AND jam_selesai', [$jadwal->jam_mulai])
                        ->orWhereRaw('? BETWEEN jam_mulai AND jam_selesai', [$jadwal->jam_selesai]);
                })
                ->pluck('ruangan_id');

            return !$occupiedRooms->contains($ruangan->id); // Pastikan ruangan tidak terisi
        });

        // Data untuk dropdown
        $dosen = Mdosen::all();
        $matkul = Mmatkul::all();
        $prodi = Mprodi::all();

        return view('jadwal.edit', compact('jadwal', 'dosen', 'ruanganTersedia', 'matkul', 'prodi'));
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
            'kelas' => 'required',
            'dosen_id' => 'required|exists:dosen,id',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'prodi_id' => 'required|exists:prodi,id',
            'kode_matkul' => 'required|exists:matkul,id',
        ]);

        // Hanya lakukan pengecekan jika ada ruangan yang dipilih
        if (!empty($validated['ruangan_id'])) {
            $occupiedRooms = Mjadwal::where('hari', $validated['hari'])
                ->where('id', '!=', $id) // Kecualikan jadwal yang sedang diedit
                ->where(function ($query) use ($validated) {
                    $query->where(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<', $validated['jam_selesai'])
                            ->where('jam_selesai', '>', $validated['jam_mulai']);
                    });
                })
                ->pluck('ruangan_id');


            if ($occupiedRooms->contains($validated['ruangan_id'])) {
                return redirect()->back()->withErrors(['ruangan_id' => 'Ruangan sudah terisi pada waktu tersebut.']);
            }
        }

        // Cek apakah dosen sudah memiliki jadwal bertabrakan (kecuali jadwal yang sedang diedit)
        $cekJadwalDosen = Mjadwal::where('hari', $request->hari)
            ->where('dosen_id', $request->dosen_id)
            ->where('id', '!=', $id) // Abaikan jadwal yang sedang diedit
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereRaw('? BETWEEN jam_mulai AND jam_selesai', [$request->jam_mulai])
                    ->orWhereRaw('? BETWEEN jam_mulai AND jam_selesai', [$request->jam_selesai]);
            })
            ->exists();

        if ($cekJadwalDosen) {
            return redirect()->back()->withErrors(['dosen_id' => 'Dosen sudah memiliki jadwal pada waktu tersebut.'])->withInput();
        }
        // Ambil semester dari matkul
        $matkul = Mmatkul::find($validated['kode_matkul']);
        if (!$matkul) {
            return redirect()->back()->withErrors(['kode_matkul' => 'Mata kuliah tidak ditemukan.'])->withInput();
        }

        $semester = $matkul->smt;
        $kelas = $validated['kelas'];

        // Cek apakah kelas dan semester sudah memiliki jadwal di waktu tersebut
        $cekJadwalKelasSmt = Mjadwal::where('hari', $validated['hari'])
            ->where('kelas', $kelas)
            ->whereHas('matkul', function ($q) use ($semester) {
                $q->where('smt', $semester);
            })
            ->where('id', '!=', $id)
            ->where(function ($query) use ($validated) {
                $query->where('jam_mulai', '<', $validated['jam_selesai'])
                    ->where('jam_selesai', '>', $validated['jam_mulai']);
            })
            ->exists();

        if ($cekJadwalKelasSmt) {
            return redirect()->back()->withErrors(['kelas' => 'Kelas dan semester ini sudah memiliki jadwal pada waktu tersebut.'])->withInput();
        }

        // Update data
        $jadwal->update($validated);

        $status = [
            'judul' => 'Berhasil',
            'pesan' => 'Jadwal berhasil diperbarui!',
            'icon' => 'success'
        ];

        return redirect()->route('jadwal.index')->with(compact('status'));
    }

    // method untuk delete semua/terpilih
    public function bulkDelete(Request $request)
    {
        if ($request->has('delete_all')) {
            Mjadwal::truncate();
            return redirect()->back()->with('success', 'Semua data berhasil dihapus!');
        }

        if ($request->has('selected')) {
            $ids = explode(',', $request->selected);
            Mjadwal::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Data terpilih berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }
}
