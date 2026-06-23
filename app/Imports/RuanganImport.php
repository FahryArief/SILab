<?php

namespace App\Imports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuanganImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['nama_ruangan'])) {
            return null;
        }

        return new Ruangan([
            'nama_ruangan' => $row['nama_ruangan'],
            'kode_ruangan' => $row['kode_ruangan'] ?? null,
            'kapasitas' => $row['kapasitas'] ?? null,
        ]);
    }
}
