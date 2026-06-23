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
}
