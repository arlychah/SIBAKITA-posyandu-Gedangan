<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'pemeriksaan_balita',
        'pemeriksaan_ibu_hamil',
        'pemeriksaan_lansia',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('verification_status', 24)->default('legacy_review')->index();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->text('return_reason')->nullable();
            });
        }

        // Existing records are retained and hidden from members until a clinician reviews them.
        foreach ($this->tables as $tableName) {
            \Illuminate\Support\Facades\DB::table($tableName)->update([
                'verification_status' => 'legacy_review',
            ]);
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign(['submitted_by']);
                $table->dropForeign(['verified_by']);
                $table->dropIndex($tableName.'_verification_status_index');
                $table->dropColumn([
                    'submitted_by',
                    'verification_status',
                    'verified_by',
                    'verified_at',
                    'return_reason',
                ]);
            });
        }
    }
};
