<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class YatimPiatu extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'nisn',
        'npsn',
        'jenjang',
        'name',
        'asal_sekolah',
        'alamat',
        'tempat_lahir',
        'tanggal_lahir',
        'imageskartukeluarga',
        'imagesktpwali',
        'imagesketerangansiswaaktif',
        'imagessuratkematian',
        'imagessurattidakmenerimabeasiswa',
        'imagesuratsktm',
        'status_data',
        'alasan_verif',
        'verif_kk',
        'alasan_kk',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function imageskartukeluarga(): Attribute
    {
        return Attribute::make(
            get: fn($imageskartukeluarga) => url('/storage/sertifikat/yatim/' . $imageskartukeluarga),
        );
    }

    protected function imagesktpwali(): Attribute
    {
        return Attribute::make(
            get: fn($imagesktpwali) => url('/storage/sertifikat/yatim/' . $imagesktpwali),
        );
    }

    protected function imagesketerangansiswaaktif(): Attribute
    {
        return Attribute::make(
            get: fn($imagesketerangansiswaaktif) => url('/storage/sertifikat/yatim/' . $imagesketerangansiswaaktif),
        );
    }

    protected function imagessuratkematian(): Attribute
    {
        return Attribute::make(
            get: fn($imagessuratkematian) => url('/storage/sertifikat/yatim/' . $imagessuratkematian),
        );
    }

    protected function imagessurattidakmenerimabeasiswa(): Attribute
    {
        return Attribute::make(
            get: fn($imagessurattidakmenerimabeasiswa) => url('/storage/sertifikat/yatim/' . $imagessurattidakmenerimabeasiswa),
        );
    }

    protected function imagesuratsktm(): Attribute
    {
        return Attribute::make(
            get: fn($imagesuratsktm) => url('/storage/sertifikat/yatim/' . $imagesuratsktm),
        );
    }
}
