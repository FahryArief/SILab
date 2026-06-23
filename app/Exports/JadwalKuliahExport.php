<?php

namespace App\Exports;

use App\Models\JadwalKuliah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JadwalKuliahExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return JadwalKuliah::select(
            'ruangan_id',
            'tahun_ajaran_id',
            'hari',
            'waktu_mulai',
            'waktu_selesai',
            'mata_kuliah',
            'dosen'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID Ruangan',
            'ID Tahun Ajaran',
            'Hari (Senin/Selasa/Rabu/Kamis/Jumat/Sabtu/Minggu)',
            'Waktu Mulai (HH:MM)',
            'Waktu Selesai (HH:MM)',
            'Mata Kuliah',
            'Dosen',
        ];
    }
}
