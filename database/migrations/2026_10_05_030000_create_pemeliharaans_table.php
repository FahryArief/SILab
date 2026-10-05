<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log/riwayat pemeliharaan (perbaikan/perawatan) Barang atau Ruangan.
     *
     * Independen dari Tahun Ajaran (tidak dikunci/difilter ke Tahun Ajaran
     * seperti Barang/Ruangan/Jadwal) — ini catatan kejadian yang bisa terjadi
     * kapan saja, ditandai tahun_ajaran_id cuma untuk konteks historis kalau
     * suatu saat dibutuhkan, bukan untuk filtering laporan.
     */
    public function up(): void
    {
        Schema::create('pemeliharaans', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_aset', ['barang', 'ruangan']);
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->onDelete('set null');
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->onDelete('set null');

            // Snapshot nama aset saat pemeliharaan dicatat, supaya laporan lama
            // tetap bisa dibaca walau barang/ruangannya sudah dihapus/berubah
            // nama — pola yang sama dipakai di AuditBarang/AuditRuangan.
            $table->string('nama_aset_snapshot');

            $table->date('tanggal_pemeliharaan');
            $table->string('jenis_perbaikan'); // mis. "Perbaikan", "Perawatan Rutin", "Penggantian Sparepart", "Lainnya"
            $table->string('kondisi_sebelum')->nullable();
            $table->string('kondisi_sesudah')->nullable();
            $table->decimal('biaya', 12, 2)->nullable();
            $table->foreignId('teknisi_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->onDelete('set null');
            $table->timestamps();

            $table->index(['jenis_aset', 'tanggal_pemeliharaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeliharaans');
    }
};
