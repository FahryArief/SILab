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

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali' => 'date',
        ];
    }

    // Relasi ke Peminjam
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Barang (Many to Many)
    public function barangs()
    {
        return $this->belongsToMany(Barang::class, 'peminjaman_barangs', 'peminjaman_id', 'barang_id')
            ->withPivot(['nama_barang_snapshot', 'barcode_snapshot', 'kondisi_snapshot'])
            ->withTimestamps();
    }

    public function getTerlambatAttribute(): bool
    {
        return $this->status === 'disetujui'
            && $this->tanggal_kembali !== null
            && now()->startOfDay()->gt($this->tanggal_kembali);
    }

    public function getHariTerlambatAttribute(): int
    {
        if (!$this->terlambat) {
            return 0;
        }

        return $this->tanggal_kembali->diffInDays(now()->startOfDay());
    }
}
