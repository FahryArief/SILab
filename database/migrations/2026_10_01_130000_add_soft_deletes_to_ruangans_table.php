<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan soft delete ke tabel ruangans.
     *
     * Tujuannya: ruangan/lab yang sudah tidak dipakai bisa "dinonaktifkan"
     * (bukan dihapus permanen) sehingga baris datanya tetap ada di database.
     * Dengan begitu, laporan yang difilter ke tahun-tahun sebelumnya tetap
     * bisa menampilkan ruangan tersebut sesuai kondisi aslinya waktu itu —
     * meski di tahun berjalan ruangan itu sudah tidak aktif/terlihat di
     * halaman Data Ruangan.
     */
    public function up(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
