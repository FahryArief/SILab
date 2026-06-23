<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $guarded = []; // <-- Tambahkan baris ini

    public function getStatusLabelAttribute()
    {
        // Logika sederhana: untuk sementara kita buat default 'Tersedia'
        // Kedepannya ini akan mengecek ke tabel booking_ruangans
        return 'Tersedia';
    }

    // Relasi ke Barang yang ada di ruangan ini
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}