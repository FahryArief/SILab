<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditRuangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_periode_id',
        'ruangan_id',
        'nama_ruangan_snapshot',
        'kode_ruangan_snapshot',
        'fasilitas_snapshot',
        'tahun_ajaran_id',
        'teknisi_id',
        'fasilitas_audit',
        'catatan',
        'tanggal_audit',
    ];

    protected $casts = [
        'fasilitas_audit' => 'array',
        'tanggal_audit' => 'datetime',
    ];

    public function auditPeriode()
    {
        return $this->belongsTo(AuditPeriode::class);
    }

    public function ruangan()
    {
        // withTrashed(): hasil audit lama tetap terhubung ke ruangan meski
        // ruangan itu sudah dinonaktifkan setelah periode audit berjalan.
        return $this->belongsTo(Ruangan::class)->withTrashed();
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
}
