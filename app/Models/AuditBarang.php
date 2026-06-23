<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_periode_id',
        'barang_id',
        'tahun_ajaran_id',
        'teknisi_id',
        'kondisi',
        'catatan',
        'tanggal_audit',
    ];

    public function auditPeriode()
    {
        return $this->belongsTo(AuditPeriode::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
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
