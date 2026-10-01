<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_ruangan',
        'keterangan',
        'fasilitas',
        'foto_ruangan',
        'kapasitas',
        'lokasi',
        'kode_ruangan',
        'terakhir_diperiksa_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getStatusLabelAttribute()
    {
        if ($this->trashed()) {
            return 'Nonaktif';
        }

        // Logika sederhana: untuk sementara kita buat default 'Tersedia'
        // Kedepannya ini akan mengecek ke tabel booking_ruangans
        return 'Tersedia';
    }

    /**
     * Ruangan dianggap "aktif pada tahun X" jika:
     * - sudah ada sejak tahun itu (created_at <= tahun), dan
     * - belum dinonaktifkan, atau baru dinonaktifkan di tahun itu/setelahnya.
     *
     * Dipakai untuk laporan historis (akreditasi dll) supaya ruangan yang
     * sudah dinonaktifkan tetap tampil kalau laporan difilter ke tahun saat
     * ruangan itu masih aktif.
     */
    public function scopeAktifPadaTahun($query, $tahun)
    {
        return $query->withTrashed()
            ->whereYear('created_at', '<=', $tahun)
            ->where(function ($q) use ($tahun) {
                $q->whereNull('deleted_at')
                  ->orWhereYear('deleted_at', '>=', $tahun);
            });
    }

    // Relasi ke Barang yang ada di ruangan ini
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}