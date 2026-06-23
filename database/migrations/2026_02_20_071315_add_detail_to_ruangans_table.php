<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->text('fasilitas')->nullable()->after('keterangan');
            $table->string('foto_ruangan')->nullable()->after('fasilitas');
        });
    }

    public function down(): void
    {
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropColumn(['fasilitas', 'foto_ruangan']);
        });
    }
};
