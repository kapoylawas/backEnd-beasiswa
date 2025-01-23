<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terdaftar extends Model
{
    use HasFactory;

    // Jika nama tabel tidak sesuai dengan konvensi, Anda bisa mendefinisikannya di sini
    protected $table = 'terdaftar';

    // Jika Anda ingin menentukan kolom yang dapat diisi massal
    protected $fillable = ['nik', 'name', 'tahun']; // Tambahkan kolom lain sesuai kebutuhan
}
