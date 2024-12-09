<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mprodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';
    protected $fillable = ['id_prodi', 'nama_prodi'];

    public function jadwal()
    {
        return $this->hasMany(Mjadwal::class, 'id_prodi');
    }

    // public function matkul()
    // {
    //     return $this->hasMany(Mmatkul::class, 'id_prodi');
    // }
}
