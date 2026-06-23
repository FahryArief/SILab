<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    // Cegah Laravel mencari tabel 'peminjamen'
    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id',
        'nama_peminjam',
        'tanggal_pinjam',
        'tanggal_kembali',
        'keperluan',
        'surat_peminjaman',
        'status',
        'catatan_admin',
    ];

    // Relasi ke Peminjam
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Barang (Many to Many)
    public function barangs()
    {
        return $this->belongsToMany(Barang::class, 'peminjaman_barangs', 'peminjaman_id', 'barang_id')->withTimestamps();
    }
}
