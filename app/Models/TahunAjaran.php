<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tahun',
        'semester',
        'is_active',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Set tahun ajaran ini menjadi aktif dan nonaktifkan yang lain.
     */
    public function activate()
    {
        // Nonaktifkan semua
        self::query()->update(['is_active' => false]);
        // Aktifkan yang ini
        $this->update(['is_active' => true]);
    }

    /**
     * Cari Tahun Ajaran yang rentang tanggalnya mencakup tanggal tertentu.
     * Berguna untuk merekonstruksi "ini kejadian di Tahun Ajaran apa" dari
     * sebuah tanggal, mis. saat mencatat histori lokasi Barang.
     */
    public static function containingDate($date)
    {
        return self::whereDate('tanggal_mulai', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('tanggal_selesai')
                  ->orWhereDate('tanggal_selesai', '>=', $date);
            })
            ->first();
    }
}
