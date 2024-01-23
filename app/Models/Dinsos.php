<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Dinsos extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipe_daftar',
        'name',
        'imagesktm',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function imagesktm(): Attribute
    {
        return Attribute::make(
            get: fn ($imagesktm) => url('/storage/sertifikat/dinsos/' . $imagesktm),
        );
    }
}
