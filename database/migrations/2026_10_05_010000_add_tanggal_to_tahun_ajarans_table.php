<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah rentang tanggal asli ke Tahun Ajaran, supaya "Tahun Ajaran" bisa
     * jadi satu-satunya acuan waktu untuk seluruh laporan (Barang, Ruangan,
     * Jadwal Kuliah), bukan lagi tahun kalender yang lepas dari model ini.
     *
     * Nullable dulu karena Tahun Ajaran yang sudah ada belum punya tanggal —
     * harus diisi manual lewat halaman Tahun Ajaran setelah migrasi ini.
     */
    public function up(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
