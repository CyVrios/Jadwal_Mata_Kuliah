<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MJadwal extends Model
{
    use HasFactory;
    
    protected $table = 'jadwal';
    
    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'dosen_id',    // Perubahan dari id_dosen
        'kelas',
        'ruangan_id',  // Perubahan dari id_ruangan
        'kode_matkul',
        'prodi_id',    // Perubahan dari id_prodi
        'mode_pembelajaran',
        'sks',
        'smt',
    ];

    // Relasi ke dosen
    public function dosen()
    {
        return $this->belongsTo(Mdosen::class, 'dosen_id');
    }

    // Relasi ke mata kuliah
    public function matkul()
    {
        return $this->belongsTo(Mmatkul::class, 'kode_matkul');
    }

    // Relasi ke ruangan
    public function ruangan()
    {
        return $this->belongsTo(Mruangan::class, 'ruangan_id');
    }

    // Relasi ke prodi
    public function prodi()
    {
        return $this->belongsTo(Mprodi::class, 'prodi_id');
    }
}
