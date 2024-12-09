<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mkelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $fillable = ['id_kelas', 'nama_kelas'];

    public function jadwal()
    {
        return $this->hasMany(Mjadwal::class, 'id_kelas');
    }
}
