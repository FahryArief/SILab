<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Pemeliharaan;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeder data dummy untuk Laporan Pemeliharaan Laboratorium.
 *
 * Membuat beberapa riwayat pemeliharaan (barang & ruangan) yang tersebar
 * di sepanjang rentang tanggal tiap Tahun Ajaran/semester yang sudah ada,
 * supaya laporan pemeliharaan per semester punya data untuk ditampilkan
 * dan diuji.
 *
 * Cara pakai:
 *   php artisan db:seed --class=Database\\Seeders\\PemeliharaanSeeder
 *
 * Catatan:
 * - Butuh data Tahun Ajaran, Barang, Ruangan, dan minimal 1 user dengan
 *   role teknisi/kepala_lab/super_admin sudah ada lebih dulu.
 * - Tahun Ajaran yang belum diisi tanggal_mulai/tanggal_selesai akan
 *   dilewati (data tanggalnya ditebak dari semester supaya aman, lihat
 *   fallbackRentangTanggal()).
 * - Aman dijalankan berkali-kali: setiap run akan menambah data baru
 *   (bukan duplikat exact, karena tanggal & kombinasi aset diacak), jadi
 *   jalankan sekali saja kecuali memang mau menambah lebih banyak dummy.
 */
class PemeliharaanSeeder extends Seeder
{
    /**
     * Jumlah riwayat pemeliharaan yang dibuat per Tahun Ajaran/semester.
     */
    private int $jumlahPerSemester = 8;

    private array $jenisPerbaikanBarang = [
        'Pembersihan & Kalibrasi Rutin',
        'Penggantian Spare Part',
        'Service Berkala',
        'Perbaikan Kabel/Konektor',
        'Upgrade Komponen',
        'Pengecekan & Pelumasan',
        'Penggantian Baterai/Power Supply',
        'Instalasi Ulang Sistem',
    ];

    private array $jenisPerbaikanRuangan = [
        'Perbaikan Instalasi Listrik',
        'Service AC Ruangan',
        'Pengecatan & Perbaikan Dinding',
        'Perbaikan Jaringan LAN/Wifi',
        'Penggantian Lampu Ruangan',
        'Pengecekan Instalasi Keselamatan',
        'Perbaikan Pintu/Kunci Ruangan',
        'Pembersihan & Perawatan Umum',
    ];

    private array $kondisiPasangan = [
        ['Rusak Ringan', 'Baik'],
        ['Rusak Berat', 'Rusak Ringan'],
        ['Kurang Baik', 'Baik'],
        ['Baik', 'Baik'], // perawatan preventif, kondisi tetap baik
        ['Rusak Ringan', 'Rusak Ringan'], // belum tuntas, masih perlu tindak lanjut
    ];

    private array $catatanContoh = [
        'Sudah dilakukan pengecekan menyeluruh, kondisi kembali normal.',
        'Sparepart diganti dengan yang baru, menunggu stok untuk unit cadangan.',
        'Perlu pemantauan lanjutan pada pemeliharaan berikutnya.',
        'Dilakukan sesuai jadwal pemeliharaan rutin semester ini.',
        'Kerusakan ditemukan saat audit inventaris, langsung ditindaklanjuti.',
        'Belum sepenuhnya normal, direkomendasikan penggantian unit.',
        null,
        null,
    ];

    public function run(): void
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai')->orderBy('id')->get();

        if ($tahunAjarans->isEmpty()) {
            $this->command?->warn('Belum ada data Tahun Ajaran. Buat Tahun Ajaran dulu sebelum menjalankan seeder ini.');
            return;
        }

        $barangs = Barang::all();
        $ruangans = Ruangan::all();
        $teknisiIds = User::whereIn('role', ['teknisi', 'kepala_lab', 'super_admin'])->pluck('id');

        if ($barangs->isEmpty() && $ruangans->isEmpty()) {
            $this->command?->warn('Belum ada data Barang maupun Ruangan. Seeder dibatalkan.');
            return;
        }

        if ($teknisiIds->isEmpty()) {
            $this->command?->warn('Belum ada user dengan role teknisi/kepala_lab/super_admin. Seeder dibatalkan.');
            return;
        }

        $totalDibuat = 0;

        foreach ($tahunAjarans as $ta) {
            [$mulai, $selesai] = $this->rentangTanggal($ta);

            if (!$mulai || !$selesai || $mulai->gt($selesai)) {
                $this->command?->warn("Lewati {$ta->nama_tahun} {$ta->semester}: rentang tanggal tidak valid.");
                continue;
            }

            // Jangan buat tanggal pemeliharaan di masa depan melebihi hari ini,
            // supaya data dummy tetap masuk akal untuk semester yang sedang berjalan.
            $batasAkhir = $selesai->isFuture() ? Carbon::today() : $selesai;
            if ($mulai->gt($batasAkhir)) {
                $batasAkhir = $mulai;
            }

            for ($i = 0; $i < $this->jumlahPerSemester; $i++) {
                $jenisAset = ($barangs->isNotEmpty() && ($ruangans->isEmpty() || random_int(0, 1) === 0))
                    ? 'barang'
                    : 'ruangan';

                if ($jenisAset === 'barang' && $barangs->isEmpty()) {
                    $jenisAset = 'ruangan';
                }
                if ($jenisAset === 'ruangan' && $ruangans->isEmpty()) {
                    $jenisAset = 'barang';
                }

                $tanggal = Carbon::createFromTimestamp(
                    random_int($mulai->timestamp, $batasAkhir->timestamp)
                )->startOfDay();

                [$kondisiSebelum, $kondisiSesudah] = $this->kondisiPasangan[array_rand($this->kondisiPasangan)];

                $data = [
                    'jenis_aset' => $jenisAset,
                    'tanggal_pemeliharaan' => $tanggal,
                    'kondisi_sebelum' => $kondisiSebelum,
                    'kondisi_sesudah' => $kondisiSesudah,
                    'biaya' => random_int(0, 10) <= 7 ? random_int(20, 1500) * 1000 : 0,
                    'teknisi_id' => $teknisiIds->random(),
                    'catatan' => $this->catatanContoh[array_rand($this->catatanContoh)],
                    'tahun_ajaran_id' => $ta->id,
                ];

                if ($jenisAset === 'barang') {
                    $barang = $barangs->random();
                    $data['barang_id'] = $barang->id;
                    $data['ruangan_id'] = null;
                    $data['nama_aset_snapshot'] = $barang->nama_barang;
                    $data['jenis_perbaikan'] = $this->jenisPerbaikanBarang[array_rand($this->jenisPerbaikanBarang)];
                } else {
                    $ruangan = $ruangans->random();
                    $data['barang_id'] = null;
                    $data['ruangan_id'] = $ruangan->id;
                    $data['nama_aset_snapshot'] = $ruangan->nama_ruangan;
                    $data['jenis_perbaikan'] = $this->jenisPerbaikanRuangan[array_rand($this->jenisPerbaikanRuangan)];
                }

                Pemeliharaan::create($data);
                $totalDibuat++;
            }

            $this->command?->info("{$ta->nama_tahun} ({$ta->semester}): {$this->jumlahPerSemester} riwayat pemeliharaan dibuat.");
        }

        $this->command?->info("Selesai. Total {$totalDibuat} riwayat pemeliharaan dummy dibuat di " . $tahunAjarans->count() . ' semester.');
    }

    /**
     * Ambil rentang tanggal_mulai/tanggal_selesai Tahun Ajaran. Kalau belum
     * diisi (masih null), tebak rentangnya dari nama_tahun + semester supaya
     * seeder tetap bisa jalan (Ganjil = Jul-Des, Genap = Jan-Jun).
     */
    private function rentangTanggal(TahunAjaran $ta): array
    {
        if ($ta->tanggal_mulai && $ta->tanggal_selesai) {
            return [Carbon::parse($ta->tanggal_mulai), Carbon::parse($ta->tanggal_selesai)];
        }

        // Tebak dari nama_tahun, format umum: "2025/2026"
        if (!preg_match('/(\d{4})\s*\/\s*(\d{4})/', $ta->nama_tahun, $m)) {
            return [null, null];
        }

        [$full, $tahunAwal, $tahunAkhir] = $m;

        if ($ta->semester === 'Ganjil') {
            return [Carbon::create((int) $tahunAwal, 7, 1), Carbon::create((int) $tahunAwal, 12, 31)];
        }

        return [Carbon::create((int) $tahunAkhir, 1, 1), Carbon::create((int) $tahunAkhir, 6, 30)];
    }
}
