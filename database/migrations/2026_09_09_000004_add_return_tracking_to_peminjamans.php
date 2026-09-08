<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('peminjamans', 'dikembalikan_at')) {
            Schema::table('peminjamans', function (Blueprint $table) {
                $table->timestamp('dikembalikan_at')->nullable()->after('tanggal_kembali');
            });
        }

        if (!Schema::hasColumn('peminjamans', 'hari_terlambat')) {
            Schema::table('peminjamans', function (Blueprint $table) {
                $table->unsignedInteger('hari_terlambat')->default(0)->after('dikembalikan_at');
            });
        }
    }

    public function down(): void
    {
        foreach (['dikembalikan_at', 'hari_terlambat'] as $column) {
            if (Schema::hasColumn('peminjamans', $column)) {
                Schema::table('peminjamans', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
