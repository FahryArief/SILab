<?php

namespace App\Services;

use App\Models\BookingRuangan;
use App\Models\JadwalKuliah;
use Illuminate\Database\Eloquent\Builder;

class BookingConflictService
{
    public function hasBookingConflict(
        int $ruanganId,
        string $tanggal,
        string $waktuMulai,
        string $waktuSelesai,
        ?int $ignoreBookingId = null
    ): bool {
        return BookingRuangan::query()
            ->where('ruangan_id', $ruanganId)
            ->whereDate('tanggal_booking', $tanggal)
            ->whereIn('status', ['pending', 'disetujui'])
            ->when($ignoreBookingId, fn (Builder $query) => $query->whereKey('!=', $ignoreBookingId))
            ->where($this->overlapConstraint($waktuMulai, $waktuSelesai))
            ->exists();
    }

    public function findClassConflict(
        int $ruanganId,
        int $tahunAjaranId,
        string $hari,
        string $waktuMulai,
        string $waktuSelesai
    ): ?JadwalKuliah {
        return JadwalKuliah::query()
            ->where('ruangan_id', $ruanganId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('hari', $hari)
            ->where($this->overlapConstraint($waktuMulai, $waktuSelesai))
            ->first();
    }

    private function overlapConstraint(string $waktuMulai, string $waktuSelesai): \Closure
    {
        return function (Builder $query) use ($waktuMulai, $waktuSelesai): void {
            $query->where('waktu_mulai', '<', $waktuSelesai)
                ->where('waktu_selesai', '>', $waktuMulai);
        };
    }
}
