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
        'uuid',
        'name',
        'ipk',
        'semester', 
        'akredetasi_kampus', 
        'akredetasi_jurusan',
        'progam_pendidikan',
        'imagetranskrip',
        'imageketerangan',
        'imagebanpt',
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

    protected function imagebanpt(): Attribute
    {
        return Attribute::make(
            get: fn ($imagebanpt) => url('/storage/banpt/' . $imagebanpt),
        );
    }
}
