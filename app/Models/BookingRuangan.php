<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRuangan extends Model
{
    // Mengizinkan semua kolom diisi secara massal
    protected $guarded = [];

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
