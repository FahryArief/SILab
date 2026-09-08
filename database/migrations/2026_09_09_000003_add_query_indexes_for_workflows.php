<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index(['status', 'tanggal_kembali']);
        });

        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->index(['ruangan_id', 'tanggal_booking', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->index(['kategori_id', 'ruangan_id']);
        });

        Schema::table('jadwal_kuliahs', function (Blueprint $table) {
            $table->index(['ruangan_id', 'tahun_ajaran_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status', 'tanggal_kembali']);
        });
        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->dropIndex(['ruangan_id', 'tanggal_booking', 'status']);
            $table->dropIndex(['user_id', 'status']);
        });
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropIndex(['kategori_id', 'ruangan_id']);
        });
        Schema::table('jadwal_kuliahs', function (Blueprint $table) {
            $table->dropIndex(['ruangan_id', 'tahun_ajaran_id', 'hari']);
        });
    }
};
