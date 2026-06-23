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
        Schema::create('audit_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->foreignId('teknisi_id')->constrained('users')->onDelete('cascade'); // Yang mengaudit
            $table->foreignId('kepala_lab_id')->nullable()->constrained('users')->onDelete('set null'); // Yang validasi
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat']);
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'divalidasi', 'ditolak'])->default('pending');
            $table->timestamp('tanggal_audit')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_barangs');
    }
};
