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
        return $this->belongsTo(Ruangan::class);
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
