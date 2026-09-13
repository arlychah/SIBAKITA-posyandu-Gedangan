<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_balita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('balita_id');
            $table->date('tanggal');
            $table->float('berat_badan');
            $table->float('tinggi_badan');
            $table->float('lingkar_kepala')->nullable();
            $table->string('status_gizi_bbu', 50)->nullable();
            $table->string('status_gizi_tbu', 50)->nullable();
            $table->string('status_gizi_bbtb', 50)->nullable();
            $table->string('asi_eksklusif', 20)->nullable();
            $table->boolean('imunisasi_bcg')->default(false);
            $table->boolean('imunisasi_dpt1')->default(false);
            $table->boolean('imunisasi_dpt2')->default(false);
            $table->boolean('imunisasi_dpt3')->default(false);
            $table->boolean('imunisasi_polio1')->default(false);
            $table->boolean('imunisasi_polio2')->default(false);
            $table->boolean('imunisasi_polio3')->default(false);
            $table->boolean('imunisasi_campak')->default(false);
            $table->integer('vitamin_a_bulan_ke')->nullable();
            $table->string('pmt_diterima', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('balita_id')->references('id')->on('balita')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_balita');
    }
};
