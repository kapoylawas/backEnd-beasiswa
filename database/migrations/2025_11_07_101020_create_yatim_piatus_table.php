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
        Schema::create('yatim_piatus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('nik')->unique();
            $table->string('nisn');
            $table->string('npsn');
            $table->string('jenjang');
            $table->string('name');
            $table->string('asal_sekolah');
            $table->text('alamat');
            $table->text('tempat_lahir');
            $table->text('tanggal_lahir');
            $table->string('imageskartukeluarga')->nullable();
            $table->string('imagesktpwali')->nullable();
            $table->string('imagesketerangansiswaaktif')->nullable();
            $table->string('imagessuratkematian')->nullable();
            $table->string('imagessurattidakmenerimabeasiswa')->nullable();
            $table->string('imagesuratsktm')->nullable();
            $table->string('status_data')->nullable();
            $table->string('alasan_verif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yatim_piatus');
    }
};
