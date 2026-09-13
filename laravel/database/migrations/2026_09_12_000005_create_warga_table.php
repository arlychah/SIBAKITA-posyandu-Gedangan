<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 200);
            $table->string('whatsapp', 15);
            $table->string('puskesmas', 150);
            $table->string('pustu', 150);
            $table->string('jenis_kelamin', 20);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir');
            $table->text('alamat')->nullable();
            $table->string('kategori', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};
