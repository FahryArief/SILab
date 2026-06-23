<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Barang::select(
            'nama_barang',
            'kategori_id',
            'ruangan_id',
            'merk',
            'deskripsi',
            'barcode',
            'kepemilikan',
            'kondisi',
            'harga',
            'status_peminjaman'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'ID Kategori',
            'ID Ruangan',
            'Merk',
            'Deskripsi',
            'Barcode',
            'Kepemilikan (Prodi/Lab)',
            'Kondisi (Baik/Rusak Ringan/Rusak Berat)',
            'Harga',
            'Status Peminjaman (Tersedia/Dipinjam/Pemeliharaan)',
        ];
    }
}
