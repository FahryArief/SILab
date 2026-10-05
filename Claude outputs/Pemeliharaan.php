<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_aset',
        'barang_id',
        'ruangan_id',
        'nama_aset_snapshot',
        'tanggal_pemeliharaan',
        'jenis_perbaikan',
        'kondisi_sebelum',
        'kondisi_sesudah',
        'biaya',
        'teknisi_id',
        'catatan',
        'tahun_ajaran_id',
    ];

    protected $casts = [
        'tanggal_pemeliharaan' => 'date',
        'biaya' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function ruangan()
    {
        // withTrashed(): riwayat pemeliharaan tetap terhubung ke ruangan
        // meski ruangan itu sudah dinonaktifkan setelah dicatat.
        return $this->belongsTo(Ruangan::class)->withTrashed();
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function getNamaAsetAttribute()
    {
        if ($this->jenis_aset === 'barang') {
            return $this->barang->nama_barang ?? $this->nama_aset_snapshot;
        }

        return $this->ruangan->nama_ruangan ?? $this->nama_aset_snapshot;
    }
}
