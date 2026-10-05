<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mencatat histori lokasi (ruangan) tiap Barang dari waktu ke waktu.
     *
     * Barang fisik tetap SATU record seperti biasa (tidak direset/digandakan
     * tiap Tahun Ajaran) — tabel ini cuma mencatat "barang ini pernah di
     * ruangan mana, sejak kapan". Setiap kali ruangan_id sebuah Barang
     * berubah, Model akan otomatis menambah satu baris baru di sini (lihat
     * App\Models\Barang::booted()). Laporan tinggal ambil baris terakhir
     * yang berlaku pada Tahun Ajaran yang dipilih untuk tahu lokasi barang
     * "pada saat itu" — persis seperti mekanisme jenis_ruangan/lab_nonaktif_sejak
     * yang sudah dipakai untuk histori Ruangan.
     */
    public function up(): void
    {
        Schema::create('barang_lokasi_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->onDelete('set null');
            $table->timestamps();

            $table->index(['barang_id', 'created_at']);
        });

        // Backfill: tiap Barang yang sudah ada diberi satu baris histori awal
        // memakai ruangan_id & created_at yang sekarang, supaya laporan
        // histori tidak kosong untuk data lama. tahun_ajaran_id dibiarkan
        // null di sini karena Tahun Ajaran belum tentu punya tanggal_mulai
        // terisi saat migrasi ini jalan (diisi manual oleh admin).
        $now = now();
        $rows = DB::table('barangs')->select('id', 'ruangan_id', 'created_at')->get();
        foreach ($rows->chunk(500) as $chunk) {
            $insert = $chunk->map(function ($barang) use ($now) {
                return [
                    'barang_id' => $barang->id,
                    'ruangan_id' => $barang->ruangan_id,
                    'tahun_ajaran_id' => null,
                    'created_at' => $barang->created_at ?? $now,
                    'updated_at' => $barang->created_at ?? $now,
                ];
            })->all();

            if (!empty($insert)) {
                DB::table('barang_lokasi_histories')->insert($insert);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_lokasi_histories');
    }
};
