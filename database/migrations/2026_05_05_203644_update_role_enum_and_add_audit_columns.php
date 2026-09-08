<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Since the initial users migration now uses string('role'), this migration
     * only needs to add audit columns to barangs and ruangans.
     * Role data migration is no longer needed because the initial migration
     * already uses the new role values directly.
     */
    public function up(): void
    {
        // Tambah kolom terakhir_diperiksa_at ke barangs
        Schema::table('barangs', function (Blueprint $table) {
            $table->timestamp('terakhir_diperiksa_at')->nullable()->after('foto_barang');
        });

        // Tambah kolom terakhir_diperiksa_at ke ruangans
        Schema::table('ruangans', function (Blueprint $table) {
            $table->timestamp('terakhir_diperiksa_at')->nullable()->after('kapasitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('terakhir_diperiksa_at');
        });

        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropColumn('terakhir_diperiksa_at');
        });
    }
};
