<?php

namespace App\Imports;

use App\Models\JadwalKuliah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JadwalKuliahImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['id_ruangan']) || !isset($row['mata_kuliah'])) {
            return null;
        }

        return new JadwalKuliah([
            'ruangan_id' => $row['id_ruangan'],
            'tahun_ajaran_id' => $row['id_tahun_ajaran'] ?? null,
            'hari' => ucfirst(strtolower($row['hari_seninselasarabukamisjumatsabtuminggu'] ?? 'Senin')),
            'waktu_mulai' => $row['waktu_mulai_hhmm'] ?? '08:00',
            'waktu_selesai' => $row['waktu_selesai_hhmm'] ?? '10:00',
            'mata_kuliah' => $row['mata_kuliah'],
            'dosen' => $row['dosen'] ?? null,
        ]);
    }
}
