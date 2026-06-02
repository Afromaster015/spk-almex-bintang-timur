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
        Schema::table('alternatifs', function (Blueprint $table) {
            if (Schema::hasColumn('alternatifs', 'periode_id')) {
                $table->dropForeign(['periode_id']);
                $table->foreign('periode_id')->references('id')->on('periodes')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alternatifs', function (Blueprint $table) {
            if (Schema::hasColumn('alternatifs', 'periode_id')) {
                $table->dropForeign(['periode_id']);
                $table->foreign('periode_id')->references('id')->on('periodes')->cascadeOnDelete();
            }
        });
    }
};
