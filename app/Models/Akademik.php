<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Akademik extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'ipk',
        'semester', 
        'akredetasi_kampus', 
        'progam_pendidikan',
        'imagetranskrip',
        'imageketerangan',
    ];

    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function imagektm(): Attribute
    {
        return Attribute::make(
            get: fn ($imagektm) => url('/storage/ktm/' . $imagektm),
        );
    }

    protected function imagetranskrip(): Attribute
    {
        return Attribute::make(
            get: fn ($imagetranskrip) => url('/storage/transkrip/' . $imagetranskrip),
        );
    }

    protected function imageketerangan(): Attribute
    {
        return Attribute::make(
            get: fn ($imageketerangan) => url('/storage/suratketerangan/' . $imageketerangan),
        );
    }
}
