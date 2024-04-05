<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LuarNegeri extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'name',
        'ipk',
        'semester', 
        'akredetasi_kampus', 
        'imageipk',
        'imagetranskrip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function imagetranskrip(): Attribute
    {
        return Attribute::make(
            get: fn ($imagetranskrip) => url('/storage/luarnegeri/' . $imagetranskrip),
        );
    }

    protected function imageipk(): Attribute
    {
        return Attribute::make(
            get: fn ($imageipk) => url('/storage/transkrip/' . $imageipk),
        );
    }
}
