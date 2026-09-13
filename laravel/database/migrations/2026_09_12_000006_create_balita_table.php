<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warga_id')->unique();
            $table->string('nama_ibu', 200)->nullable();
            $table->string('nama_ayah', 200)->nullable();
            $table->timestamps();

            $table->foreign('warga_id')->references('id')->on('warga')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balita');
    }
};
