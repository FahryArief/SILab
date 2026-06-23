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
        Schema::table('ruangans', function (Blueprint $table) {
            $table->string('kode_ruangan')->unique()->nullable()->after('nama_ruangan');
        });

        // Generate kode_ruangan untuk data yang sudah ada
        $ruangans = \App\Models\Ruangan::all();
        foreach ($ruangans as $ruangan) {
            $kode = 'RM-' . strtoupper(str_replace(' ', '', substr($ruangan->nama_ruangan, 0, 8))) . '-' . str_pad($ruangan->id, 3, '0', STR_PAD_LEFT);
            $ruangan->update(['kode_ruangan' => $kode]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropColumn('kode_ruangan');
        });
    }
};
