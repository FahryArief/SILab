<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder sekali-pakai untuk menyiapkan data historis akreditasi prodi:
 *
 * - 2024 : Lab di GKB 3.10 & GKB 3.11
 * - 2025 : Lab GKB pindah ke SFS 2.2 & SFS 2.3 (GKB jadi kelas biasa,
 *          BUKAN dihapus — tetap ada sebagai ruangan, cuma bukan lab lagi),
 *          plus lab baru SFS 2.1 (Lab Project, tanpa perangkat komputer).
 *
 * Aman dijalankan berulang kali (idempotent): pakai firstOrCreate/firstOrNew
 * dan mengecek data yang sudah ada sebelum menambah barang baru, supaya
 * tidak dobel kalau script ini di-run lebih dari sekali.
 *
 * Jalankan dengan:
 *   php artisan db:seed --class=AkreditasiLabHistoriSeeder
 *
 * CATATAN: Sebagian besar jumlah unit & harga di bawah ini adalah ASUMSI
 * yang wajar (bukan data asli) karena memang belum ditentukan secara
 * spesifik. Silakan edit angkanya di bagian konfigurasi di bawah sebelum
 * menjalankan seeder ini, atau edit langsung datanya lewat halaman Data
 * Barang / Data Ruangan setelah seeder ini jalan.
 */
class AkreditasiLabHistoriSeeder extends Seeder
{
    // ──────────────────────────────────────────────
    // KONFIGURASI — ubah di sini sesuai kebutuhan
    // ──────────────────────────────────────────────

    /** Tanggal GKB 3.10 / 3.11 mulai tercatat sebagai lab. */
    private string $tglGkbDibuat = '2024-08-15';

    /** Tanggal lab pindah ke SFS (GKB jadi kelas biasa, SFS 2.1/2.2/2.3 mulai aktif). */
    private string $tglPindah = '2025-09-01';

    /**
     * Barang "per kursi" (jumlah sama di tiap lab komputer: GKB 3.10, GKB
     * 3.11 [sebelum pindah], SFS 2.2, SFS 2.3) — 30 unit / lab sesuai data
     * Mouse & Keyboard yang sudah ada.
     */
    private int $jumlahPerKursi = 30;

    /** Barang infrastruktur per lab komputer (bukan per-kursi). */
    private array $infraLabKomputer = [
        'TV'          => 1,
        'Switch'      => 2,
        'Kabel LAN'   => 30, // 1 per unit PC
        'Kabel HDMI'  => 2,
        'APAR'        => 2,
        'Lemari'      => 2,
    ];

    /** Barang untuk SFS 2.1 (Lab Project, tanpa PC/Mouse/Keyboard/Monitor). */
    private array $itemSfs21 = [
        'Meja'       => 10,
        'Kursi'      => 20,
        'TV'         => 1,
        'Switch'     => 1,
        'Kabel LAN'  => 5,
        'Kabel HDMI' => 2,
        'APAR'       => 2,
        'Mouse Pad'  => 5,
        'Lemari'     => 3,
        'Printer'    => 2,
        'Kulkas'     => 1,
        'Dispenser'  => 1,
        'Loker'      => 20,
    ];

    /** Estimasi harga satuan (Rupiah) — SILAKAN SESUAIKAN dengan harga asli. */
    private array $hargaEstimasi = [
        'PC' => 5000000, 'Monitor' => 1500000, 'Meja' => 500000, 'Kursi' => 300000,
        'Mouse Pad' => 20000, 'TV' => 3000000, 'Switch' => 400000, 'Kabel LAN' => 15000,
        'Kabel HDMI' => 50000, 'APAR' => 350000, 'Lemari' => 800000, 'Printer' => 1500000,
        'Kulkas' => 2000000, 'Dispenser' => 500000, 'Loker' => 1000000,
    ];

    /** Kategori untuk tiap jenis barang BARU (Mouse & Keyboard reuse kategori yang sudah ada). */
    private array $kategoriMap = [
        'PC' => 'Komputer', 'Monitor' => 'Komputer',
        'Mouse Pad' => 'Peripheral',
        'Meja' => 'Furnitur', 'Kursi' => 'Furnitur', 'Lemari' => 'Furnitur',
        'TV' => 'Elektronik', 'Kulkas' => 'Elektronik', 'Dispenser' => 'Elektronik',
        'Switch' => 'Jaringan', 'Kabel LAN' => 'Jaringan', 'Kabel HDMI' => 'Jaringan',
        'APAR' => 'K3',
        'Printer' => 'Perkantoran', 'Loker' => 'Perkantoran',
    ];

    /** Prefix barcode per jenis barang (mengikuti konvensi INV.LAB-TRPL.xx yang sudah ada). */
    private array $prefixBarcode = [
        'PC' => 'INV.LAB-TRPL.PC', 'Mouse' => 'INV.LAB-TRPL.MS', 'Keyboard' => 'INV.LAB-TRPL.KB',
        'Monitor' => 'INV.LAB-TRPL.MN', 'Meja' => 'INV.LAB-TRPL.MJ', 'Kursi' => 'INV.LAB-TRPL.KR',
        'TV' => 'INV.LAB-TRPL.TV', 'Switch' => 'INV.LAB-TRPL.SW', 'Kabel LAN' => 'INV.LAB-TRPL.KL',
        'Kabel HDMI' => 'INV.LAB-TRPL.KH', 'APAR' => 'INV.LAB-TRPL.AP', 'Mouse Pad' => 'INV.LAB-TRPL.MP',
        'Lemari' => 'INV.LAB-TRPL.LM', 'Printer' => 'INV.LAB-TRPL.PR', 'Kulkas' => 'INV.LAB-TRPL.KU',
        'Dispenser' => 'INV.LAB-TRPL.DS', 'Loker' => 'INV.LAB-TRPL.LK',
    ];

    public function run(): void
    {
        $tglGkbDibuat = Carbon::parse($this->tglGkbDibuat);
        $tglPindah = Carbon::parse($this->tglPindah);

        // ──────────────────────────────────────────────
        // 1. Ruangan GKB 3.10 & 3.11 — pastikan ada, lalu jadikan "Kelas"
        //    (bukan lab lagi) sejak tanggal pindah. TIDAK dihapus/diarsipkan.
        // ──────────────────────────────────────────────
        $gkb310 = Ruangan::withTrashed()->firstOrCreate(
            ['nama_ruangan' => 'GKB 3.10'],
            ['kapasitas' => 40, 'lokasi' => 'Gedung Kuliah Bersama (GKB)', 'jenis_ruangan' => 'Lab']
        );
        $gkb311 = Ruangan::withTrashed()->firstOrCreate(
            ['nama_ruangan' => 'GKB 3.11'],
            ['kapasitas' => 80, 'lokasi' => 'Gedung Kuliah Bersama (GKB)', 'jenis_ruangan' => 'Lab']
        );
        $this->pastikanKodeRuangan($gkb310);
        $this->pastikanKodeRuangan($gkb311);

        foreach ([$gkb310, $gkb311] as $r) {
            // created_at perlu update langsung lewat query builder karena
            // Eloquent akan selalu menimpa created_at saat save() dipanggil.
            DB::table('ruangans')->where('id', $r->id)->update(['created_at' => $tglGkbDibuat]);
            $r->refresh();
            $r->update([
                'jenis_ruangan' => 'Kelas',
                'lab_nonaktif_sejak' => $tglPindah,
            ]);
        }
        $this->command->info('GKB 3.10 & 3.11: created_at di-set ke '.$tglGkbDibuat->toDateString().', dialihfungsikan jadi Kelas sejak '.$tglPindah->toDateString().'.');

        // ──────────────────────────────────────────────
        // 2. Ruangan SFS 2.1 (baru), SFS 2.2 & SFS 2.3 (pindahan dari GKB)
        // ──────────────────────────────────────────────
        $sfs21 = Ruangan::firstOrCreate(
            ['nama_ruangan' => 'SFS 2.1'],
            ['kapasitas' => 40, 'lokasi' => 'Gedung SFS', 'keterangan' => 'Lab Project', 'jenis_ruangan' => 'Lab']
        );
        $sfs22 = Ruangan::firstOrCreate(
            ['nama_ruangan' => 'SFS 2.2'],
            ['kapasitas' => $gkb310->kapasitas ?? 40, 'lokasi' => 'Gedung SFS', 'jenis_ruangan' => 'Lab']
        );
        $sfs23 = Ruangan::firstOrCreate(
            ['nama_ruangan' => 'SFS 2.3'],
            ['kapasitas' => $gkb311->kapasitas ?? 80, 'lokasi' => 'Gedung SFS', 'jenis_ruangan' => 'Lab']
        );
        foreach ([$sfs21, $sfs22, $sfs23] as $r) {
            $this->pastikanKodeRuangan($r);
            DB::table('ruangans')->where('id', $r->id)->update(['created_at' => $tglPindah]);
        }
        $this->command->info('SFS 2.1, 2.2, 2.3: dipastikan ada, created_at di-set ke '.$tglPindah->toDateString().'.');

        // ──────────────────────────────────────────────
        // 3. Pindahkan barang existing (Mouse & Keyboard) dari GKB ke SFS
        //    GKB 3.10 -> SFS 2.2, GKB 3.11 -> SFS 2.3
        // ──────────────────────────────────────────────
        $dipindahDari310 = Barang::where('ruangan_id', $gkb310->id)->update(['ruangan_id' => $sfs22->id]);
        $dipindahDari311 = Barang::where('ruangan_id', $gkb311->id)->update(['ruangan_id' => $sfs23->id]);
        $this->command->info("Barang dipindah: {$dipindahDari310} unit dari GKB 3.10 -> SFS 2.2, {$dipindahDari311} unit dari GKB 3.11 -> SFS 2.3.");

        // ──────────────────────────────────────────────
        // 4. Lengkapi barang di SFS 2.2 & SFS 2.3 (item "per kursi" + infra)
        //    Hanya menambah yang BELUM ada di ruangan itu (aman di-run ulang).
        // ──────────────────────────────────────────────
        foreach (['SFS 2.2' => $sfs22, 'SFS 2.3' => $sfs23] as $label => $ruangan) {
            foreach (['PC', 'Monitor', 'Meja', 'Kursi', 'Mouse Pad'] as $nama) {
                $this->lengkapiBarang($nama, $ruangan->id, $this->jumlahPerKursi, $tglPindah);
            }
            foreach ($this->infraLabKomputer as $nama => $jumlah) {
                $this->lengkapiBarang($nama, $ruangan->id, $jumlah, $tglPindah);
            }
            $this->command->info("Barang lab lengkap untuk {$label}.");
        }

        // ──────────────────────────────────────────────
        // 5. Barang untuk SFS 2.1 (Lab Project)
        // ──────────────────────────────────────────────
        foreach ($this->itemSfs21 as $nama => $jumlah) {
            $this->lengkapiBarang($nama, $sfs21->id, $jumlah, $tglPindah);
        }
        $this->command->info('Barang Lab Project SFS 2.1 selesai dibuat.');

        $this->command->info('Selesai. Cek halaman Data Ruangan & Data Barang untuk verifikasi.');
    }

    /**
     * Pastikan ruangan punya kode_ruangan (mengikuti konvensi yang dipakai
     * RuanganController::store()), kalau belum ada.
     */
    private function pastikanKodeRuangan(Ruangan $ruangan): void
    {
        if ($ruangan->kode_ruangan) {
            return;
        }
        $kode = 'RM-' . strtoupper(str_replace(' ', '', substr($ruangan->nama_ruangan, 0, 8))) . '-' . str_pad($ruangan->id, 3, '0', STR_PAD_LEFT);
        $ruangan->update(['kode_ruangan' => $kode]);
    }

    /**
     * Tambah barang jenis $nama di ruangan $ruanganId sampai jumlahnya
     * mencapai $jumlahTarget (hitung yang sudah ada dulu, supaya aman
     * dijalankan berulang kali tanpa duplikasi).
     */
    private function lengkapiBarang(string $nama, int $ruanganId, int $jumlahTarget, Carbon $tanggal): void
    {
        $sudahAda = Barang::where('ruangan_id', $ruanganId)->where('nama_barang', $nama)->count();
        $kurang = $jumlahTarget - $sudahAda;
        if ($kurang <= 0) {
            return;
        }

        $kategoriId = $this->resolveKategoriId($nama);
        $prefix = $this->prefixBarcode[$nama] ?? ('INV.LAB-TRPL.' . strtoupper(substr($nama, 0, 2)));
        $nomorAwal = $this->nomorBarcodeBerikutnya($prefix);
        $harga = $this->hargaEstimasi[$nama] ?? null;

        $rows = [];
        for ($i = 0; $i < $kurang; $i++) {
            $nomor = $nomorAwal + $i;
            $rows[] = [
                'nama_barang' => $nama,
                'kategori_id' => $kategoriId,
                'ruangan_id' => $ruanganId,
                'merk' => null,
                'deskripsi' => null,
                'barcode' => $prefix . str_pad($nomor, 2, '0', STR_PAD_LEFT),
                'foto_barang' => null,
                'kepemilikan' => 'Lab',
                'kondisi' => 'Baik',
                'status_peminjaman' => 'Tersedia',
                'harga' => $harga,
                'created_at' => $tanggal,
                'updated_at' => $tanggal,
            ];
        }

        DB::table('barangs')->insert($rows);
    }

    /**
     * Cari kategori_id yang sudah dipakai utk barang bernama sama (supaya
     * tidak bikin kategori baru yang tumpang-tindih dengan yang sudah ada),
     * atau buat/pakai kategori umum sesuai pemetaan di $kategoriMap.
     */
    private function resolveKategoriId(string $nama): int
    {
        $existing = Barang::where('nama_barang', $nama)->whereNotNull('kategori_id')->first();
        if ($existing) {
            return $existing->kategori_id;
        }

        $namaKategori = $this->kategoriMap[$nama] ?? 'Lain-lain';
        return Kategori::firstOrCreate(['nama_kategori' => $namaKategori])->id;
    }

    /**
     * Nomor urut berikutnya untuk barcode dengan prefix tertentu, dilanjut
     * dari nomor terbesar yang sudah ada (supaya tidak bentrok unique).
     */
    private function nomorBarcodeBerikutnya(string $prefix): int
    {
        $existingBarcodes = Barang::where('barcode', 'like', $prefix . '%')->pluck('barcode');
        $max = 0;
        foreach ($existingBarcodes as $barcode) {
            if (preg_match('/(\d+)$/', $barcode, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }
        return $max + 1;
    }
}
