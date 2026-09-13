<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lansia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warga_id')->unique();
            $table->timestamps();

            $table->foreign('warga_id')->references('id')->on('warga')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lansia');
    }
};
