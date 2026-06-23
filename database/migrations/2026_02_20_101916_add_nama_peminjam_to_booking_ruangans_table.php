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
    Schema::table('booking_ruangans', function (Blueprint $table) {
        // Mengubah user_id agar boleh kosong (nullable)
        $table->unsignedBigInteger('user_id')->nullable()->change();

        // Menambahkan kolom nama untuk peminjam tanpa akun
        $table->string('nama_peminjam')->nullable()->after('user_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_ruangans', function (Blueprint $table) {
            //
        });
    }
};
