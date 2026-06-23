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
Schema::create('barangs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
    $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
    $table->string('nama_barang');
    $table->string('merk')->nullable();
    $table->string('barcode')->unique(); // Untuk scan kamera/scanner
    $table->integer('jumlah_total');
    $table->integer('stok_tersedia');
    $table->text('deskripsi')->nullable();
    $table->string('foto_barang')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};