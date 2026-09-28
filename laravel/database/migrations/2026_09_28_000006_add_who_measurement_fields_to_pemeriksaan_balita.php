<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_balita', function (Blueprint $table) {
            $table->string('metode_pengukuran', 16)->nullable(); // standing | recumbent
            $table->string('rujukan_bbtb', 8)->nullable(); // WFH | WFL
            $table->float('z_score_bbtb')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_balita', function (Blueprint $table) {
            $table->dropColumn(['metode_pengukuran', 'rujukan_bbtb', 'z_score_bbtb']);
        });
    }
};
