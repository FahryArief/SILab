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
        Schema::create('audit_periodes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode'); // e.g. "Audit Mei - Juni 2026"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('tipe', ['barang', 'ruangan', 'semua'])->default('semua');
            $table->foreignId('kepala_lab_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->enum('status', ['open', 'dilaporkan', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_periodes');
    }
};
