<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';
    protected $fillable = ['nama_ruangan'];

    public function jadwal()
    {
        return $this->hasMany(Mjadwal::class, 'ruangan_id');
    }
}
