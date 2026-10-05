<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'nama_barang',
        'kategori_id',
        'ruangan_id',
        'merk',
        'deskripsi',
        'barcode',
        'foto_barang',
        'kepemilikan',
        'kondisi',
        'harga',
        'status_peminjaman',
        'terakhir_diperiksa_at',
    ];

    // Relasi ke Kategori (Opsional tapi baik ditambahkan sekarang)
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Relasi ke Ruangan (Opsional tapi baik ditambahkan sekarang)
    // withTrashed(): barang yang ruangannya sudah dinonaktifkan tetap bisa
    // menampilkan nama ruangan aslinya (untuk histori/laporan).
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class)->withTrashed();
    }

    // Relasi ke Peminjaman (Many to Many)
    public function peminjamans()
    {
        return $this->belongsToMany(Peminjaman::class, 'peminjaman_barangs', 'barang_id', 'peminjaman_id')->withTimestamps();
    }

    // Histori lokasi (ruangan) barang ini dari waktu ke waktu.
    public function lokasiHistories()
    {
        return $this->hasMany(BarangLokasiHistori::class);
    }

    /**
     * Catat baris histori lokasi baru untuk ruangan_id barang ini saat ini,
     * ditandai dengan Tahun Ajaran yang sedang aktif (kalau ada).
     */
    public function catatLokasiHistori(): void
    {
        if (!$this->ruangan_id) {
            return;
        }

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $this->lokasiHistories()->create([
            'ruangan_id' => $this->ruangan_id,
            'tahun_ajaran_id' => $tahunAjaranAktif?->id,
        ]);
    }

    /**
     * Ruangan barang ini "pada" Tahun Ajaran tertentu, berdasarkan histori
     * lokasi yang tercatat — bukan ruangan_id saat ini. Dipakai untuk
     * laporan historis (akreditasi dll). Kalau Tahun Ajaran tidak diberi,
     * atau belum ada histori yang cocok, jatuh balik ke ruangan saat ini.
     */
    public function ruanganPadaTahunAjaran(?int $tahunAjaranId = null)
    {
        if (!$tahunAjaranId) {
            return $this->ruangan;
        }

        $tahunAjaran = TahunAjaran::find($tahunAjaranId);
        if (!$tahunAjaran) {
            return $this->ruangan;
        }

        $batasAkhir = $tahunAjaran->tanggal_selesai ?? now();

        $histori = $this->lokasiHistories()
            ->where('created_at', '<=', $batasAkhir)
            ->orderByDesc('created_at')
            ->first();

        if (!$histori) {
            return $this->ruangan;
        }

        return Ruangan::withTrashed()->find($histori->ruangan_id);
    }

    protected static function booted()
    {
        static::created(function (Barang $barang) {
            $barang->catatLokasiHistori();
        });

        static::updated(function (Barang $barang) {
            if ($barang->wasChanged('ruangan_id')) {
                $barang->catatLokasiHistori();
            }
        });
    }
}
