<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds additional security constraints and indexes:
     * - Unique index on barangs.barcode (already declared unique in initial migration, but ensuring it)
     * - Index on users.role for faster role-based queries
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        // 1. Ensure booking_ruangans.user_id is nullable for existing databases
        Schema::table('booking_ruangans', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // Constraint ini sudah ditangani di migration create_barangs_table dan refactor_barang_and_peminjaman
        // Jadi kita tidak perlu mendefinisikannya ulang untuk menghindari bentrok index.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->dropUnique(['peminjaman_id', 'barang_id']);
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
        });
    }
};
