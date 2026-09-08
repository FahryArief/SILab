<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRuangan extends Model
{
    protected $fillable = [
        'user_id',
        'nama_peminjam',
        'ruangan_id',
        'tanggal_booking',
        'waktu_mulai',
        'waktu_selesai',
        'keperluan',
        'surat_peminjaman',
        'catatan_admin',
        'status',
    ];

    // Relasi ke tabel User (Peminjam)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel Ruangan
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
