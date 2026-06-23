<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKuliah extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruangan_id',
        'tahun_ajaran_id',
        'hari',
        'waktu_mulai',
        'waktu_selesai',
        'mata_kuliah',
        'dosen',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
