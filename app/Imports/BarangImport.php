<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip empty rows
        if (!isset($row['nama_barang']) || !isset($row['barcode'])) {
            return null;
        }

        return new Barang([
            'nama_barang' => $row['nama_barang'],
            'kategori_id' => $row['id_kategori'] ?? null,
            'ruangan_id' => $row['id_ruangan'] ?? null,
            'merk' => $row['merk'] ?? null,
            'deskripsi' => $row['deskripsi'] ?? null,
            'barcode' => $row['barcode'],
            'kepemilikan' => $row['kepemilikan_prodilab'] ?? 'Prodi',
            'kondisi' => $row['kondisi_baikrusak_ringanrusak_berat'] ?? 'Baik',
            'harga' => $row['harga'] ?? null,
            'status_peminjaman' => $row['status_peminjaman_tersediadipinjampemeliharaan'] ?? 'Tersedia',
        ]);
    }
}
