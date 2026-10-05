<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangLokasiHistori extends Model
{
    protected $table = 'barang_lokasi_histories';

    protected $fillable = [
        'barang_id',
        'ruangan_id',
        'tahun_ajaran_id',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class)->withTrashed();
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
