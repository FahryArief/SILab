<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->string('nama_barang_snapshot')->nullable()->after('barang_id');
            $table->string('barcode_snapshot')->nullable()->after('nama_barang_snapshot');
            $table->string('kondisi_snapshot')->nullable()->after('barcode_snapshot');
        });

        Schema::table('audit_ruangans', function (Blueprint $table) {
            $table->string('nama_ruangan_snapshot')->nullable()->after('ruangan_id');
            $table->string('kode_ruangan_snapshot')->nullable()->after('nama_ruangan_snapshot');
            $table->text('fasilitas_snapshot')->nullable()->after('kode_ruangan_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->dropColumn(['nama_barang_snapshot', 'barcode_snapshot', 'kondisi_snapshot']);
        });
        Schema::table('audit_ruangans', function (Blueprint $table) {
            $table->dropColumn(['nama_ruangan_snapshot', 'kode_ruangan_snapshot', 'fasilitas_snapshot']);
        });
    }
};
