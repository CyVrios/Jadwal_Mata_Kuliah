<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Mjadwal_2;
use App\Models\Mruangan;
use App\Models\Mdosen; // Assuming Mdosen is the model for Dosen
use App\Models\Mprodi; // Assuming Mprodi is the model for Program Studi
use App\Models\Mmatkul; // Assuming Mmatkul is the model for Mata Kuliah
use Illuminate\Http\Request;

class Cjadwal_2 extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rows = Mjadwal_2::with(['matkul', 'dosen', 'ruangan', 'prodi'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->orderBy('tanggal')
            ->get();

        // Grouping: hari -> jam
        $jadwalGrouped = $rows->groupBy('hari')->map(function ($g) {
            return $g->groupBy(function ($i) {
                return $i->jam_mulai . '-' . $i->jam_selesai;
            });
        });

        // <-- ini yang sebelumnya hilang
        $ruangan = Mruangan::orderBy('nama_ruangan')->get();
        $matkul  = Mmatkul::orderBy('nama_matkul')->get();
        $dosen   = Mdosen::orderBy('nama_dosen')->get();
        $prodi   = Mprodi::orderBy('nama_prodi')->get();

        return view('jadwal_2.index', compact(
            'jadwalGrouped',
            'ruangan',
            'matkul',
            'dosen',
            'prodi'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

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






    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'tanggal_mulai' => 'required|date',
    //         'ruangan_id' => 'required|integer',
    //         'mata_kuliah' => 'required|array',
    //         'jam_mulai' => 'required|array',
    //         'jam_selesai' => 'required|array',
    //         'dosen_id' => 'required|array',
    //     ]);

    //     $tanggalMulai = Carbon::parse($request->tanggal_mulai);

    //     for ($pertemuan = 0; $pertemuan < 8; $pertemuan++) {
    //         for ($i = 0; $i < count($request->mata_kuliah); $i++) {
    //             Mjadwal_2::create([
    //                 'hari' => $tanggalMulai->translatedFormat('l'),
    //                 'tanggal' => $tanggalMulai->format('Y-m-d'),
    //                 'ruangan_id' => $request->ruangan_id,
    //                 'kode_matkul' => $request->mata_kuliah[$i],
    //                 'jam_mulai' => $request->jam_mulai[$i],
    //                 'jam_selesai' => $request->jam_selesai[$i],
    //                 'dosen_id' => $request->dosen_id[$i],
    //             ]);
    //         }
    //         $tanggalMulai->addWeeks(2);
    //     }

    //     return back()->with('success', 'Jadwal berkala berhasil dibuat!');
    // }

    /**
     * Display the specified resource.
     */
    public function show(Mjadwal_2 $mjadwal_2)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mjadwal_2 $mjadwal_2)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mjadwal_2 $mjadwal_2)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mjadwal_2 $mjadwal_2)
    {
        //
    }
}
