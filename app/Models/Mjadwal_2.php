<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mjadwal_2 extends Model
{
    use HasFactory;
    protected $table = 'jadwal_2';

    protected $fillable = [
        'hari',
        'tanggal', // Tanggal untuk jadwal berkala
        'jam_mulai',
        'jam_selesai',
        'dosen_id',
        'kelas',
        'ruangan_id',  
        'kode_matkul',
        'prodi_id', 
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
