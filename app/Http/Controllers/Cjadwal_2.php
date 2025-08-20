<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Mjadwal_2;
use App\Models\Mruangan;
use App\Models\Mdosen;
use App\Models\Mprodi;
use App\Models\Mmatkul;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Jadwal2Export;

class Cjadwal_2 extends Controller
{
    /**
     * INDEX + Filter + Data dropdown
     */
    public function index(Request $request)
    {
        $smtFilter   = $request->get('smt');
        $prodiFilter = $request->get('prodi');

        $rows = Mjadwal_2::with(['matkul', 'dosen', 'ruangan', 'prodi'])
            ->when($smtFilter, function ($q) use ($smtFilter) {
                $q->whereHas('matkul', function ($qq) use ($smtFilter) {
                    $qq->where('smt', $smtFilter);
                });
            })
            ->when($prodiFilter, function ($q) use ($prodiFilter) {
                $q->where('prodi_id', $prodiFilter);
            })
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai')
            ->orderBy('tanggal')
            ->get();

        // Group hari -> jam (untuk tampilan seperti yang kamu mau)
        $jadwalGrouped = $rows->groupBy('hari')->map(function ($g) {
            return $g->groupBy(fn($i) => $i->jam_mulai . '-' . $i->jam_selesai);
        });

        // Dropdown form
        $ruangan = Mruangan::orderBy('nama_ruangan')->get();
        $matkul  = Mmatkul::orderBy('nama_matkul')->get();
        $dosen   = Mdosen::orderBy('nama_dosen')->get();
        $prodi   = Mprodi::orderBy('nama_prodi')->get();

        // Filter dropdown
        $semesters = Mmatkul::whereIn('id', Mjadwal_2::pluck('kode_matkul'))
            ->select('smt')->distinct()->pluck('smt');
        $prodiList = Mprodi::select('id', 'nama_prodi')->pluck('nama_prodi', 'id');

        return view('jadwal_2.index', compact(
            'jadwalGrouped',
            'ruangan',
            'matkul',
            'dosen',
            'prodi',
            'semesters',
            'prodiList',
            'smtFilter',
            'prodiFilter'
        ));
    }

    /**
     * STORE (jadwal berkala 8x) + cek tabrakan per item
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'hari' => 'required|string',
            'ruangan_id' => 'required|exists:ruangan,id',
            'prodi_id' => 'required|exists:prodi,id',
            'kode_matkul' => 'required|array',
            'kode_matkul.*' => 'exists:matkul,id',
            'jam_mulai' => 'required|array',
            'jam_mulai.*' => 'required|date_format:H:i',
            'jam_selesai' => 'required|array',
            'jam_selesai.*' => 'required|date_format:H:i',
            'dosen_id' => 'required|array',
            'dosen_id.*' => 'exists:dosen,id',
        ]);

        $tanggalAwal = Carbon::parse($request->tanggal_mulai);
        $hari = $request->hari;

        for ($i = 0; $i < 8; $i++) {
            $tanggal = $tanggalAwal->copy()->addWeeks($i * 2);

            foreach ($request->kode_matkul as $index => $matkulId) {

                // ✅ Cek apakah ruangan sudah terpakai pada jam tersebut
                $cekRuangan = Mjadwal_2::where('hari', $hari)
                    ->where('tanggal', $tanggal->format('Y-m-d'))
                    ->where('ruangan_id', $request->ruangan_id)
                    ->where(function ($q) use ($request, $index) {
                        $q->where('jam_mulai', '<', $request->jam_selesai[$index])
                            ->where('jam_selesai', '>', $request->jam_mulai[$index]);
                    })
                    ->exists();

                if ($cekRuangan) {
                    return back()->withErrors(['ruangan_id' => 'Ruangan sudah digunakan pada jam ' . $request->jam_mulai[$index] . ' - ' . $request->jam_selesai[$index]])->withInput();
                }

                Mjadwal_2::create([
                    'tanggal'     => $tanggal->format('Y-m-d'),
                    'hari'        => $hari,
                    'jam_mulai'   => $request->jam_mulai[$index],
                    'jam_selesai' => $request->jam_selesai[$index],
                    'kode_matkul' => $matkulId,
                    'ruangan_id'  => $request->ruangan_id,
                    'prodi_id'    => $request->prodi_id,
                    'dosen_id'    => $request->dosen_id[$index],
                    'kelas'       => $request->kelas[$index] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Jadwal berkala berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $jadwal = Mjadwal_2::with(['matkul', 'dosen', 'ruangan'])->findOrFail($id);

        return response()->json([
            'id'         => $jadwal->id,
            'hari'       => $jadwal->hari,
            'tanggal'    => $jadwal->tanggal,
            'jam_mulai'  => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
            'kode_matkul' => $jadwal->kode_matkul,
            'kelas'      => $jadwal->kelas,
            'dosen_id'   => $jadwal->dosen_id,
            'ruangan_id' => $jadwal->ruangan_id,
            'prodi_id'   => $jadwal->prodi_id,
        ]);
    }

    public function update(Request $request, $id)
{
    $jadwal = Mjadwal_2::findOrFail($id);

    $request->validate([
        'tanggal'     => 'required|date',
        'hari'        => 'required|string',
        'jam_mulai'   => 'required|date_format:H:i',
        'jam_selesai' => 'required|date_format:H:i',
        'kode_matkul' => 'required|exists:matkul,id',
        'ruangan_id'  => 'required|exists:ruangan,id',
        'prodi_id'    => 'required|exists:prodi,id',
        'dosen_id'    => 'required|exists:dosen,id',
        'kelas'       => 'nullable|string',
    ]);

    $jadwal->update([
        'tanggal'     => $request->tanggal,
        'hari'        => $request->hari,
        'jam_mulai'   => $request->jam_mulai,
        'jam_selesai' => $request->jam_selesai,
        'kode_matkul' => $request->kode_matkul,
        'ruangan_id'  => $request->ruangan_id,
        'prodi_id'    => $request->prodi_id,
        'dosen_id'    => $request->dosen_id,
        'kelas'       => $request->kelas,
    ]);

    return redirect()->route('jadwal_2.index')->with('status', [
        'judul' => 'Berhasil',
        'pesan' => 'Jadwal berhasil diperbarui!',
        'icon'  => 'success'
    ]);
}


    /**
     * Cek ketersediaan ruangan (opsional by tanggal; fallback by hari)
     */
    public function checkAvailableRooms(Request $request)
    {
        $exists = Mjadwal_2::where('ruangan_id', $request->ruangan_id)
            ->where('hari', $request->hari)
            ->where('tanggal', $request->tanggal)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Cek bentrok Dosen (by tanggal/hari + jam)
     */
    public function checkDosen(Request $request)
    {
        $hari       = $request->input('hari');
        $tanggal    = $request->input('tanggal');
        $jamMulai   = $request->input('jam_mulai');
        $jamSelesai = $request->input('jam_selesai');
        $dosenId    = $request->input('dosen_id');

        if (!$dosenId || !$jamMulai || !$jamSelesai || (!$hari && !$tanggal)) {
            return response()->json(['available' => false, 'msg' => 'Param kurang'], 400);
        }

        $q = Mjadwal_2::where('dosen_id', $dosenId);
        if ($tanggal) $q->where('tanggal', $tanggal);
        elseif ($hari) $q->where('hari', $hari);

        $exists = $q->where(function ($qq) use ($jamMulai, $jamSelesai) {
            $qq->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai);
        })->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Cek bentrok kelas + semester (by tanggal/hari + jam)
     */
    public function checkKelasSemester(Request $request)
    {
        $hari       = $request->input('hari');
        $tanggal    = $request->input('tanggal');
        $jamMulai   = $request->input('jam_mulai');
        $jamSelesai = $request->input('jam_selesai');
        $kelas      = $request->input('kelas');
        $kodeMatkul = $request->input('kode_matkul');

        if (!$kelas || !$kodeMatkul || !$jamMulai || !$jamSelesai || (!$hari && !$tanggal)) {
            return response()->json(['available' => false, 'msg' => 'Param kurang'], 400);
        }

        $smt = Mmatkul::find($kodeMatkul)?->smt;
        if (!$smt) return response()->json(['available' => true]);

        $q = Mjadwal_2::where('kelas', $kelas)->whereHas('matkul', function ($qq) use ($smt) {
            $qq->where('smt', $smt);
        });

        if ($tanggal) $q->where('tanggal', $tanggal);
        elseif ($hari) $q->where('hari', $hari);

        $exists = $q->where(function ($qq) use ($jamMulai, $jamSelesai) {
            $qq->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai);
        })->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Export Excel
     */
    public function export(Request $request)
    {
        return Excel::download(
            new Jadwal2Export($request->get('smt'), $request->get('prodi')),
            'jadwal_2.xlsx'
        );
    }

    /**
     * Cek slot kosong (list jam + ruangan/dosen kosong) per HARI
     */
    public function cekSlotKosong(Request $request)
    {
        $hari = $request->input('hari');
        if (!$hari) return response()->json(['hasil' => []]);

        $jamStart = Carbon::createFromTime(7, 0);
        $jamEnd   = Carbon::createFromTime(15, 0);

        $slot = [];
        while ($jamStart->lt($jamEnd)) {
            $next = $jamStart->copy()->addMinutes(45);
            $slot[] = ['jam_mulai' => $jamStart->format('H:i'), 'jam_selesai' => $next->format('H:i')];
            $jamStart = $next;
        }

        $jadwalHari = Mjadwal_2::where('hari', $hari)->get();
        $hasil = [];

        foreach ($slot as $s) {
            $jm = $s['jam_mulai'];
            $js = $s['jam_selesai'];
            $terpakai = $jadwalHari->filter(fn($j) => !($j->jam_selesai <= $jm || $j->jam_mulai >= $js));
            $ruangTerpakai = $terpakai->pluck('ruangan_id')->unique();
            $dosenTerpakai = $terpakai->pluck('dosen_id')->unique();

            $ruanganKosong = Mruangan::whereNotIn('id', $ruangTerpakai)->pluck('nama_ruangan');
            $dosenKosong   = Mdosen::whereNotIn('id', $dosenTerpakai)->pluck('nama_dosen');

            $hasil[] = [
                'jam_mulai' => $jm,
                'jam_selesai' => $js,
                'ruangan_kosong' => $ruanganKosong,
                'dosen_kosong' => $dosenKosong,
            ];
        }
        return response()->json(['hasil' => $hasil]);
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(Request $request)
    {
        if ($request->has('delete_all')) {
            Mjadwal_2::truncate();
            return back()->with('status', ['judul' => 'OK', 'pesan' => 'Semua data dihapus', 'icon' => 'success']);
        }
        if ($request->has('selected')) {
            $ids = explode(',', $request->selected);
            Mjadwal_2::whereIn('id', $ids)->delete();
            return back()->with('status', ['judul' => 'OK', 'pesan' => 'Data terpilih dihapus', 'icon' => 'success']);
        }
        return back()->with('status', ['judul' => 'Gagal', 'pesan' => 'Tidak ada data dipilih', 'icon' => 'error']);
    }
}
