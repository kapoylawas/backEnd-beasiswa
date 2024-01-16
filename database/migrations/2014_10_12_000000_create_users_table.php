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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nokk')->unique();
            $table->string('name');
            $table->string('nohp');
            $table->string('email')->unique();
            $table->string('gender');
            $table->string('kecamatan');
            $table->string('codepos');
            $table->string('rt');
            $table->string('rw');
            $table->string('alamat');
            $table->string('status_terkirim');
            $table->integer('status');
            $table->integer('status_pendaftar');
            $table->string('imagektp')->nullable();
            $table->string('imagekk')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('nim')->nullable();
            $table->string('ktm')->nullable();
            $table->string('universitas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('imageaktifkampus')->nullable();
            $table->string('imagesuratpernyataan')->nullable();
            $table->string('imageakrekampus')->nullable();
            $table->string('pilih_universitas')->nullable();
            $table->string('jenis_universitas')->nullable();
            $table->integer('step')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
