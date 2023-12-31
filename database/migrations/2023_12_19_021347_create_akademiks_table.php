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
        Schema::create('akademiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('ipk');
            $table->string('universitas');
            $table->string('jurusan');
            $table->string('semester');
            $table->string('nim');
            $table->string('imagektm');
            $table->string('akredetasi_kampus');
            $table->string('akredetasi_jurusan');
            $table->string('progam_pendidikan');
            $table->string('imageaktifkampus');
            $table->string('imagesuratpernyataan');
            $table->string('imagetranskrip');
            $table->string('imageketerangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akademiks');
    }
};
