<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPeriode extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe',
        'kepala_lab_id',
        'catatan',
        'status',
        'catatan_revisi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kepalaLab()
    {
        return $this->belongsTo(User::class, 'kepala_lab_id');
    }

    public function auditBarangs()
    {
        return $this->hasMany(AuditBarang::class);
    }

    public function auditRuangans()
    {
        return $this->hasMany(AuditRuangan::class);
    }
}
