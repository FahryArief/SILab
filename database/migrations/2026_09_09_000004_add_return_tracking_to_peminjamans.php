<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM peminjamans');
        $existingColumns = array_column($columns, 'Field');

        Schema::table('peminjamans', function (Blueprint $table) use ($existingColumns) {
            if (!in_array('dikembalikan_at', $existingColumns)) {
                $table->timestamp('dikembalikan_at')->nullable()->after('tanggal_kembali');
            }
            if (!in_array('hari_terlambat', $existingColumns)) {
                $table->unsignedInteger('hari_terlambat')->default(0)->after('dikembalikan_at');
            }
        });
    }

    public function down(): void
    {
        $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM peminjamans');
        $existingColumns = array_column($columns, 'Field');

        Schema::table('peminjamans', function (Blueprint $table) use ($existingColumns) {
            if (in_array('dikembalikan_at', $existingColumns)) {
                $table->dropColumn('dikembalikan_at');
            }
            if (in_array('hari_terlambat', $existingColumns)) {
                $table->dropColumn('hari_terlambat');
            }
        });
    }
};
