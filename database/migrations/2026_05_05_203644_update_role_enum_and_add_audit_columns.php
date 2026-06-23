<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah struktur role enum
        // Tambah opsi enum baru sementara
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'operator', 'prodi', 'peminjam', 'super_admin', 'teknisi', 'kepala_lab', 'ka_prodi') DEFAULT 'peminjam'");

        // Update data lama
        DB::statement("UPDATE users SET role = 'super_admin' WHERE role = 'admin'");
        DB::statement("UPDATE users SET role = 'teknisi' WHERE role = 'operator'");
        DB::statement("UPDATE users SET role = 'ka_prodi' WHERE role = 'prodi'");

        // Hapus opsi enum lama
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'teknisi', 'kepala_lab', 'ka_prodi', 'peminjam') DEFAULT 'peminjam'");

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

        // Kembalikan enum ke semula
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");
        DB::statement("UPDATE users SET role = 'operator' WHERE role = 'teknisi'");
        DB::statement("UPDATE users SET role = 'prodi' WHERE role = 'ka_prodi'");
        DB::statement("UPDATE users SET role = 'prodi' WHERE role = 'kepala_lab'"); // fallback
        
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'operator', 'prodi', 'peminjam') DEFAULT 'peminjam'");
    }
};
