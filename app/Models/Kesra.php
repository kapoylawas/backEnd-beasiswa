<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Kesra extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'tipe_kesra',
        'tipe_sertifikat',
        'name',
        'imagesertifikat',
        'imagepiagamnonmuslim',
        'tahun',
        'nama_organisasi',
        'alamat_organisasi',
        'nama_ponpes',
        'alamat_ponpes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function imagesertifikat(): Attribute
    {
        return Attribute::make(
            get: fn ($imagesertifikat) => url('/storage/sertifikat/kesra/' . $imagesertifikat),
        );
    }

    protected function imagepiagamnonmuslim(): Attribute
    {
        return Attribute::make(
            get: fn ($imagepiagamnonmuslim) => url('/storage/sertifikat/kesra/' . $imagepiagamnonmuslim),
        );
    }
}
