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
        Schema::create('dinsos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('tipe_daftar');
            $table->string('name');
            $table->string('penghasilan_orangtua')->nullable();
            $table->string('pekerjaan_orangtua')->nullable();
            $table->string('status_rumah')->nullable();
            $table->string('status_kendaraan')->nullable();
            $table->string('jumlah_kendaraan')->nullable();
            $table->string('imagesktm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinsos');
    }
};
