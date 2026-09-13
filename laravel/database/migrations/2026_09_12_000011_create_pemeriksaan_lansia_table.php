<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_lansia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lansia_id');
            $table->date('tanggal');
            $table->float('berat_badan')->nullable();
            $table->float('tinggi_badan')->nullable();
            $table->integer('tekanan_darah_sistolik')->nullable();
            $table->integer('tekanan_darah_diastolik')->nullable();
            $table->float('gula_darah_puasa')->nullable();
            $table->float('gula_darah_sewaktu')->nullable();
            $table->float('kolesterol')->nullable();
            $table->float('asam_urat')->nullable();
            $table->string('skrining_jiwa', 100)->nullable();
            $table->string('penglihatan', 100)->nullable();
            $table->string('pendengaran', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('lansia_id')->references('id')->on('lansia')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_lansia');
    }
};
