<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_pemeriksaan', 24);
            $table->unsignedBigInteger('pemeriksaan_id');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi', 24); // submitted, edited, verified, returned
            $table->json('sebelum')->nullable();
            $table->json('sesudah')->nullable();
            $table->text('alasan')->nullable();
            $table->timestamps();

            $table->index(['jenis_pemeriksaan', 'pemeriksaan_id'], 'audit_pemeriksaan_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_audit_logs');
    }
};
