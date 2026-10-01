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
}
