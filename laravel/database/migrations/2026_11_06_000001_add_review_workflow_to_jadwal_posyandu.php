<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_posyandu', function (Blueprint $table) {
            $table->string('approval_status', 24)->default('approved')->index();
            $table->text('review_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_posyandu', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['approval_status']);
            $table->dropColumn(['approval_status', 'review_note', 'reviewed_by', 'reviewed_at']);
        });
    }
};
