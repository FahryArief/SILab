<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->string('nama_barang_snapshot')->nullable()->after('barang_id');
            $table->string('barcode_snapshot')->nullable()->after('nama_barang_snapshot');
            $table->string('kondisi_snapshot')->nullable()->after('barcode_snapshot');
            $table->unique(['peminjaman_id', 'barang_id']);
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->dropUnique(['peminjaman_id', 'barang_id']);
            $table->dropColumn([
                'nama_barang_snapshot',
                'barcode_snapshot',
                'kondisi_snapshot',
            ]);
        });
    }
};
