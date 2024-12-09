<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mmatkul extends Model
{
    use HasFactory;

    protected $table = 'matkul';
    protected $fillable = ['nama_matkul', 'kode_matkul', 'smt'];


    // public function prodi()
    // {
    //     return $this->belongsTo(Mprodi::class, 'id_prodi');
    // }

    public function jadwal()
    {
        return $this->hasMany(Mjadwal::class, 'kode_matkul');
    }
}
