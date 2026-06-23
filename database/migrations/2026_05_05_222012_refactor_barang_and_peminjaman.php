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
        Schema::disableForeignKeyConstraints();

        // 1. Drop existing FKs from peminjamans and the table itself
        Schema::dropIfExists('peminjamans');

        // 2. Modify barangs table
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['jumlah_total', 'stok_tersedia']);
            // The existing `barcode` will serve as `kode_inventaris`
            $table->enum('kepemilikan', ['Prodi', 'Lab'])->default('Prodi');
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status_peminjaman', ['Tersedia', 'Dipinjam', 'Pemeliharaan'])->default('Tersedia');
            $table->decimal('harga', 15, 2)->nullable();
        });

        // 3. Recreate peminjamans with new structure
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nama_peminjam')->nullable();
            // Tidak ada barang_id atau jumlah_pinjam karena pindah ke pivot
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->text('keperluan');
            $table->string('surat_peminjaman')->nullable(); // Untuk non-TRPL
            $table->enum('status', ['pending', 'divalidasi_teknisi', 'disetujui', 'ditolak', 'dikembalikan'])->default('pending');
            $table->timestamps();
        });

        // 4. Create pivot table for Peminjaman <-> Barang
        Schema::create('peminjaman_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('peminjaman_barangs');
        Schema::dropIfExists('peminjamans');
        
        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('jumlah_total')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->dropColumn(['kepemilikan', 'kondisi', 'status_peminjaman', 'harga']);
        });

        // Restore old peminjamans (simplified version for rollback)
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nama_peminjam')->nullable();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah_pinjam');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->text('keperluan');
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan'])->default('pending');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }
};
