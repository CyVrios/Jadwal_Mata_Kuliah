<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mjadwal extends Model
{
    use HasFactory;
    protected $table = 'jadwal';
    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'id_dosen',
        'kelas',
        'id_ruangan',
        'kode_matkul',
        'id_prodi',
        'mode_pembelajaran',
        'sks',
        'smt',
    ];


    public function dosen()
    {
        return $this->belongsTo(Mdosen::class, 'id_dosen');
    }

    // public function kelas()
    // {
    //     return $this->belongsTo(Mkelas::class, 'id_kelas');
    // }

    public function matkul()
    {
        return $this->belongsTo(Mmatkul::class, 'kode_matkul');
    }

    public function ruangan()
    {
        return $this->belongsTo(Mruangan::class, 'id_ruangan');
    }

    public function prodi()
    {
        return $this->belongsTo(Mprodi::class, 'id_prodi');
    }
}
