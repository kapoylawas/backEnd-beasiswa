<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class NonAkademik extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'semester',
        'akredetasi_kampus', 
        'jenis_sertifikat',
        'imagesertifikat',
        'imageakredetasi',
        'tahun',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function imagesertifikat(): Attribute
    {
        return Attribute::make(
            get: fn ($imagesertifikat) => url('/storage/sertifikat/dispora/' . $imagesertifikat),
        );
    }

    protected function imageakredetasi(): Attribute
    {
        return Attribute::make(
            get: fn ($imageakredetasi) => url('/storage/imageakrekampus/' . $imageakredetasi),
        );
    }
}
