<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mdosen extends Model
{
    use HasFactory;
    protected $table = 'dosen';
    protected $fillable = ['nidn_dosen', 'nama_dosen'];

    public function jadwal()
    {
        return $this->hasMany(Mjadwal::class, 'dosen_id');
    }
}
