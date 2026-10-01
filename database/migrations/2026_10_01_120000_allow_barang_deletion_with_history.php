<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah FK barang_id di peminjaman_barangs dan audit_barangs:
     * - Jadikan nullable
     * - Ganti onDelete dari CASCADE menjadi SET NULL
     *
     * Dengan ini barang bisa dihapus meskipun ada riwayat peminjaman/audit,
     * karena snapshot (nama, barcode, kondisi) sudah tersimpan di tabel terkait.
     */
    public function up(): void
    {
        // ──────────────────────────────────────────────
        // 1. Isi snapshot yang masih kosong sebelum FK diubah
        // ──────────────────────────────────────────────
        DB::statement("
            UPDATE peminjaman_barangs pb
            INNER JOIN barangs b ON pb.barang_id = b.id
            SET pb.nama_barang_snapshot = COALESCE(pb.nama_barang_snapshot, b.nama_barang),
                pb.barcode_snapshot     = COALESCE(pb.barcode_snapshot, b.barcode),
                pb.kondisi_snapshot     = COALESCE(pb.kondisi_snapshot, b.kondisi)
            WHERE pb.nama_barang_snapshot IS NULL
               OR pb.barcode_snapshot IS NULL
               OR pb.kondisi_snapshot IS NULL
        ");

        DB::statement("
            UPDATE audit_barangs ab
            INNER JOIN barangs b ON ab.barang_id = b.id
            SET ab.nama_barang_snapshot = COALESCE(ab.nama_barang_snapshot, b.nama_barang),
                ab.barcode_snapshot     = COALESCE(ab.barcode_snapshot, b.barcode),
                ab.kondisi_snapshot     = COALESCE(ab.kondisi_snapshot, b.kondisi)
            WHERE ab.nama_barang_snapshot IS NULL
               OR ab.barcode_snapshot IS NULL
               OR ab.kondisi_snapshot IS NULL
        ");

        // ──────────────────────────────────────────────
        // 2. peminjaman_barangs: ubah FK barang_id
        // ──────────────────────────────────────────────
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });

        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_id')->nullable()->change();
            $table->foreign('barang_id')
                  ->references('id')->on('barangs')
                  ->onDelete('set null');
        });

        // ──────────────────────────────────────────────
        // 3. audit_barangs: ubah FK barang_id
        // ──────────────────────────────────────────────
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });

        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_id')->nullable()->change();
            $table->foreign('barang_id')
                  ->references('id')->on('barangs')
                  ->onDelete('set null');
        });
    }

    /**
     * Kembalikan FK ke CASCADE (rollback).
     */
    public function down(): void
    {
        // peminjaman_barangs: kembalikan ke cascade
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });
        // Hapus row yang barang_id-nya null sebelum restore NOT NULL
        DB::table('peminjaman_barangs')->whereNull('barang_id')->delete();
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_id')->nullable(false)->change();
            $table->foreign('barang_id')
                  ->references('id')->on('barangs')
                  ->onDelete('cascade');
        });

        // audit_barangs: kembalikan ke cascade
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });
        DB::table('audit_barangs')->whereNull('barang_id')->delete();
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_id')->nullable(false)->change();
            $table->foreign('barang_id')
                  ->references('id')->on('barangs')
                  ->onDelete('cascade');
        });
    }
};
