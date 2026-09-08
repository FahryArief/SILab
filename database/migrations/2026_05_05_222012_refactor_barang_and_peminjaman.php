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

        // 1. We will not drop the table, but modify it instead.
        // Schema::dropIfExists('peminjamans');

        // 2. Modify barangs table
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['jumlah_total', 'stok_tersedia']);
            // The existing `barcode` will serve as `kode_inventaris`
            $table->enum('kepemilikan', ['Prodi', 'Lab'])->default('Prodi');
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status_peminjaman', ['Tersedia', 'Dipinjam', 'Pemeliharaan'])->default('Tersedia');
            $table->decimal('harga', 15, 2)->nullable();
        });

        // 3. Modify peminjamans table
        Schema::table('peminjamans', function (Blueprint $table) {
            // Because SQLite has limited ALTER TABLE support, especially with Enums and dropping columns with FKs,
            // we have to do this carefully or just add columns. However, Laravel 11 handles SQLite schema changes better.
            
            // Drop old columns
            $table->dropForeign(['barang_id']);
            $table->dropColumn(['barang_id', 'jumlah_pinjam']);

            // Modify user_id to be nullable
            $table->string('surat_peminjaman')->nullable()->after('keperluan');
            
            // Note: modifying Enums in SQLite using change() often fails.
            // A safer way is to just leave it as string if we did it earlier, but since the initial was enum, we should change it to string first.
            $table->string('status')->default('pending')->change();
        });

        // 4. Create pivot table for Peminjaman <-> Barang
        Schema::create('peminjaman_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->unique(['peminjaman_id', 'barang_id']);
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

        // Restore old peminjamans by re-adding columns
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn(['nama_peminjam', 'surat_peminjaman']);
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah_pinjam')->default(1);
        });

        Schema::enableForeignKeyConstraints();
    }
};
