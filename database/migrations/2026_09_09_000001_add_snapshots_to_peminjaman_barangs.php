<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM peminjaman_barangs');
        $existingColumns = array_column($columns, 'Field');

        Schema::table('peminjaman_barangs', function (Blueprint $table) use ($existingColumns) {
            if (!in_array('nama_barang_snapshot', $existingColumns)) {
                $table->string('nama_barang_snapshot')->nullable()->after('barang_id');
            }
            if (!in_array('barcode_snapshot', $existingColumns)) {
                $table->string('barcode_snapshot')->nullable()->after('nama_barang_snapshot');
            }
            if (!in_array('kondisi_snapshot', $existingColumns)) {
                $table->string('kondisi_snapshot')->nullable()->after('barcode_snapshot');
            }
        });
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
