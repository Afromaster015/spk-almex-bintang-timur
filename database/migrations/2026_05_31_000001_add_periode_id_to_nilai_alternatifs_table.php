<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai_alternatifs', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('kriteria_id')->constrained('periodes')->nullOnDelete();
            $table->unique(['alternatif_id', 'kriteria_id', 'periode_id'], 'nilai_alternatif_unique_periode');
        });
    }

    public function down(): void
    {
        Schema::table('nilai_alternatifs', function (Blueprint $table) {
            $table->dropUnique('nilai_alternatif_unique_periode');
            $table->dropConstrainedForeignId('periode_id');
        });
    }
};
