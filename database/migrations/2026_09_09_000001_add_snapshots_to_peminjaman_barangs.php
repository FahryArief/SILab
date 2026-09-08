<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('peminjaman_barangs', 'nama_barang_snapshot')) {
            Schema::table('peminjaman_barangs', function (Blueprint $table) {
                $table->string('nama_barang_snapshot')->nullable()->after('barang_id');
            });
        }

        if (!Schema::hasColumn('peminjaman_barangs', 'barcode_snapshot')) {
            Schema::table('peminjaman_barangs', function (Blueprint $table) {
                $table->string('barcode_snapshot')->nullable()->after('nama_barang_snapshot');
            });
        }

        if (!Schema::hasColumn('peminjaman_barangs', 'kondisi_snapshot')) {
            Schema::table('peminjaman_barangs', function (Blueprint $table) {
                $table->string('kondisi_snapshot')->nullable()->after('barcode_snapshot');
            });
        }
    }

    public function down(): void
    {
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->dropColumn([
                'nama_barang_snapshot',
                'barcode_snapshot',
                'kondisi_snapshot',
            ]);
        });
    }
};
