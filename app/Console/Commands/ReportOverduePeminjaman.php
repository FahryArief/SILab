<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Illuminate\Console\Command;

class ReportOverduePeminjaman extends Command
{
    protected $signature = 'peminjaman:report-overdue';

    protected $description = 'Report approved borrowings whose return date has passed';

    public function handle(): int
    {
        $count = Peminjaman::query()
            ->where('status', 'disetujui')
            ->whereDate('tanggal_kembali', '<', now()->toDateString())
            ->count();

        $this->info("{$count} peminjaman terlambat terdeteksi.");

        return self::SUCCESS;
    }
}
