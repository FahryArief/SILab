<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update enum to include disetujui and revisi
        DB::statement("ALTER TABLE audit_periodes MODIFY COLUMN status ENUM('open', 'dilaporkan', 'closed', 'disetujui', 'revisi') DEFAULT 'open'");
        
        // Update existing 'closed' to 'disetujui'
        DB::statement("UPDATE audit_periodes SET status = 'disetujui' WHERE status = 'closed'");
        
        // Remove closed and keep only what we need
        DB::statement("ALTER TABLE audit_periodes MODIFY COLUMN status ENUM('open', 'dilaporkan', 'disetujui', 'revisi') DEFAULT 'open'");
        
        Schema::table('audit_periodes', function (Blueprint $table) {
            $table->text('catatan_revisi')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('audit_periodes', function (Blueprint $table) {
            $table->dropColumn('catatan_revisi');
        });
        
        DB::statement("ALTER TABLE audit_periodes MODIFY COLUMN status ENUM('open', 'dilaporkan', 'closed', 'disetujui', 'revisi') DEFAULT 'open'");
        DB::statement("UPDATE audit_periodes SET status = 'closed' WHERE status = 'disetujui'");
        DB::statement("UPDATE audit_periodes SET status = 'open' WHERE status = 'revisi'");
        DB::statement("ALTER TABLE audit_periodes MODIFY COLUMN status ENUM('open', 'dilaporkan', 'closed') DEFAULT 'open'");
    }
};
