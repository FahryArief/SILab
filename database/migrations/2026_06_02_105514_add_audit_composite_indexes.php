<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_barangs', function (Blueprint $table) {
            // Composite unique index for fast updateOrCreate lookups
            $table->unique(['audit_periode_id', 'barang_id'], 'audit_barangs_periode_barang_unique');
        });

        Schema::table('audit_ruangans', function (Blueprint $table) {
            // Composite unique index for fast updateOrCreate lookups
            $table->unique(['audit_periode_id', 'ruangan_id'], 'audit_ruangans_periode_ruangan_unique');
        });

        Schema::table('audit_periodes', function (Blueprint $table) {
            $table->index('status');
            $table->index('kepala_lab_id');
        });
    }

    public function down(): void
    {
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->dropUnique('audit_barangs_periode_barang_unique');
        });

        Schema::table('audit_ruangans', function (Blueprint $table) {
            $table->dropUnique('audit_ruangans_periode_ruangan_unique');
        });

        Schema::table('audit_periodes', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['kepala_lab_id']);
        });
    }
};
