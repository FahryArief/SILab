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
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->foreignId('audit_periode_id')->nullable()->after('id')->constrained('audit_periodes')->onDelete('cascade');
            $table->dropForeign(['kepala_lab_id']);
            $table->dropColumn(['status', 'kepala_lab_id']);
        });

        Schema::table('audit_ruangans', function (Blueprint $table) {
            $table->foreignId('audit_periode_id')->nullable()->after('id')->constrained('audit_periodes')->onDelete('cascade');
            $table->dropForeign(['kepala_lab_id']);
            $table->dropColumn(['status', 'kepala_lab_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_barangs', function (Blueprint $table) {
            $table->dropForeign(['audit_periode_id']);
            $table->dropColumn('audit_periode_id');
            $table->foreignId('kepala_lab_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'divalidasi', 'ditolak'])->default('pending');
        });

        Schema::table('audit_ruangans', function (Blueprint $table) {
            $table->dropForeign(['audit_periode_id']);
            $table->dropColumn('audit_periode_id');
            $table->foreignId('kepala_lab_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'divalidasi', 'ditolak'])->default('pending');
        });
    }
};
