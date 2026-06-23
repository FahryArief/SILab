<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_ruangans', function (Blueprint $table) {
            $table->longText('fasilitas_audit')->nullable()->after('teknisi_id');
            // Catatan tetap dipertahankan, tapi kondisi dihapus.
            // Karena SQLite mungkin bermasalah dengan dropColumn, Laravel biasanya bisa handle ini dengan doctrine/dbal di versi baru
            $table->dropColumn('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_ruangans', function (Blueprint $table) {
            $table->dropColumn('fasilitas_audit');
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
        });
    }
};
