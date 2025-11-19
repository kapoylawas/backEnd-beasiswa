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
        'name',
        'asal_sekolah',
        'alamat',
        'imageskartukeluarga',
        'imagesuratsktm',
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

    protected function imagesuratsktm(): Attribute
    {
        return Attribute::make(
            get: fn($imagesuratsktm) => url('/storage/sertifikat/yatim/' . $imagesuratsktm),
        );
    }
}
