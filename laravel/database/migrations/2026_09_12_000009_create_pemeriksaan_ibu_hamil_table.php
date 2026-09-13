<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_ibu_hamil', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ibu_hamil_id');
            $table->date('tanggal');
            $table->integer('kehamilan_ke')->nullable();
            $table->integer('usia_kehamilan')->nullable();
            $table->float('berat_badan')->nullable();
            $table->integer('tekanan_darah_sistolik')->nullable();
            $table->integer('tekanan_darah_diastolik')->nullable();
            $table->float('lila')->nullable();
            $table->float('tinggi_fundus')->nullable();
            $table->integer('detak_jantung_janin')->nullable();
            $table->boolean('ttd_diberikan')->default(false);
            $table->integer('jumlah_ttd')->nullable();
            $table->string('imunisasi_tt', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('ibu_hamil_id')->references('id')->on('ibu_hamil')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_ibu_hamil');
    }
};
