<?php

namespace App\Exports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RuanganExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Ruangan::select('nama_ruangan', 'kode_ruangan', 'kapasitas')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Ruangan',
            'Kode Ruangan',
            'Kapasitas',
        ];
    }
}
