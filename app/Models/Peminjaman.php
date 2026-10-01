<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Ambil data barang untuk tampilan, termasuk yang sudah dihapus.
     * Menggunakan snapshot dari tabel pivot sebagai fallback.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBarangItemsAttribute()
    {
        // Ambil dari pivot table langsung (termasuk row yang barang_id = NULL)
        $pivotRows = DB::table('peminjaman_barangs')
            ->where('peminjaman_id', $this->id)
            ->get();

        return $pivotRows->map(function ($pivot) {
            // Coba ambil data barang asli jika masih ada
            $barang = $pivot->barang_id ? Barang::find($pivot->barang_id) : null;

            return (object) [
                'id'           => $pivot->barang_id,
                'nama_barang'  => $barang->nama_barang ?? $pivot->nama_barang_snapshot ?? 'Barang Dihapus',
                'barcode'      => $barang->barcode ?? $pivot->barcode_snapshot ?? '-',
                'kondisi'      => $barang->kondisi ?? $pivot->kondisi_snapshot ?? '-',
                'is_deleted'   => is_null($barang),
            ];
        });
    }
}
