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
        'jenis_ruangan',
        'lab_nonaktif_sejak',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'lab_nonaktif_sejak' => 'date',
    ];

    public function getStatusLabelAttribute()
    {
        if ($this->trashed()) {
            return 'Nonaktif';
        }

        if ($this->jenis_ruangan && $this->jenis_ruangan !== 'Lab') {
            return $this->jenis_ruangan; // mis. "Kelas"
        }

        // Logika sederhana: untuk sementara kita buat default 'Tersedia'
        // Kedepannya ini akan mengecek ke tabel booking_ruangans
        return 'Tersedia';
    }

    /**
     * Ruangan dianggap "aktif sebagai LAB pada tahun X" jika:
     * - sudah ada sejak tahun itu (created_at <= tahun),
     * - belum diarsipkan/dihapus, atau baru diarsipkan di tahun itu/setelahnya,
     * - dan belum beralih fungsi dari Lab, atau baru beralih fungsi di tahun
     *   itu/setelahnya (mis. ruangan yang jadi kelas biasa mulai tahun 2025
     *   tetap dihitung sebagai lab untuk laporan tahun 2024 dan 2025).
     *
     * Dipakai untuk laporan historis (akreditasi dll) supaya ruangan yang
     * sudah dinonaktifkan/dialihfungsikan tetap tampil kalau laporan
     * difilter ke tahun saat ruangan itu masih berfungsi sebagai lab.
     */
    public function scopeAktifPadaTahun($query, $tahun)
    {
        return $query->withTrashed()
            ->whereYear('created_at', '<=', $tahun)
            ->where(function ($q) use ($tahun) {
                $q->whereNull('deleted_at')
                  ->orWhereYear('deleted_at', '>=', $tahun);
            })
            ->where(function ($q) use ($tahun) {
                $q->whereNull('lab_nonaktif_sejak')
                  ->orWhereYear('lab_nonaktif_sejak', '>=', $tahun);
            });
    }

    // Relasi ke Barang yang ada di ruangan ini
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}