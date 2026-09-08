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
        'dikembalikan_at',
        'hari_terlambat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali' => 'date',
            'dikembalikan_at' => 'datetime',
            'hari_terlambat' => 'integer',
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
            && today()->gt($this->tanggal_kembali);
    }

    public function getHariTerlambatAttribute(): int
    {
        if ($this->status === 'dikembalikan') {
            return (int) $this->attributes['hari_terlambat'];
        }

        if (!$this->terlambat) {
            return 0;
        }

        return $this->tanggal_kembali->diffInDays(today());
    }

    public function getDikembalikanTerlambatAttribute(): bool
    {
        return $this->status === 'dikembalikan' && $this->hari_terlambat > 0;
    }
}
