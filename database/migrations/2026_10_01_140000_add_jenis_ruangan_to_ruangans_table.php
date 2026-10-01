<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan penanda fungsi ruangan.
     *
     * Beda dengan soft delete (deleted_at) yang berarti ruangan benar-benar
     * sudah tidak dipakai/diarsipkan — kasus ini untuk ruangan yang TETAP
     * aktif dipakai, tapi beralih fungsi (misalnya dari "Lab" jadi "Kelas
     * biasa"). Ruangan itu tidak hilang dari sistem, cuma tidak dihitung
     * lagi sebagai ruangan laboratorium sejak tanggal tertentu — berguna
     * untuk laporan historis per tahun (akreditasi, dll).
     */
    public function up(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->string('jenis_ruangan', 20)->default('Lab')->after('kode_ruangan');
            $table->date('lab_nonaktif_sejak')->nullable()->after('jenis_ruangan');
        });
    }

    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropColumn(['jenis_ruangan', 'lab_nonaktif_sejak']);
        });
    }
};
