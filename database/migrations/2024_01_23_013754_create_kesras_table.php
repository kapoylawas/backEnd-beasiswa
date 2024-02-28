<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kesras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('tipe_kesra')->nullable();
            $table->integer('tipe_sertifikat')->nullable();
            $table->string('name')->nullable();
            $table->integer('nama_ponpes')->nullable();
            $table->integer('alamat_ponpes')->nullable();
            $table->integer('nama_organisasi')->nullable();
            $table->integer('alamat_organisasi')->nullable();
            $table->string('imagesertifikat')->nullable();
            $table->string('imagepiagamnonmuslim')->nullable();
            $table->string('tahun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesras');
    }
};
