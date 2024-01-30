<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nik',
        'nokk',
        'name',
        'nohp',
        'email',
        'gender',
        'kecamatan',
        'codepos',
        'rt',
        'rw',
        'alamat',
        'imagektp',
        'imagekk',
        'password',
        'status',
        'status_pendaftar',
        'status_terkirim',
        'nim',
        'ktm',
        'universitas',
        'alamat_univ',
        'jurusan',
        'imageaktifkampus',
        'imagesuratpernyataan',
        'imageakrekampus',
        'pilih_universitas',
        'jenis_universitas',
        'step',
        'tipe_beasiswa',
        'kota',
        'status_wa',
        'status_email',
        'status_finish',
    ];

    protected function imagektp(): Attribute
    {
        return Attribute::make(
            get: fn ($imagektp) => url('/storage/ktp/' . $imagektp),
        );
    }

    protected function imagekk(): Attribute
    {
        return Attribute::make(
            get: fn ($imagekk) => url('/storage/kk/' . $imagekk),
        );
    }

    protected function ktm(): Attribute
    {
        return Attribute::make(
            get: fn ($ktm) => url('/storage/ktm/' . $ktm),
        );
    }

    protected function imageaktifkampus(): Attribute
    {
        return Attribute::make(
            get: fn ($imageaktifkampus) => url('/storage/imageaktifkampus/' . $imageaktifkampus),
        );
    }

    protected function imagesuratpernyataan(): Attribute
    {
        return Attribute::make(
            get: fn ($imagesuratpernyataan) => url('/storage/imagesuratpernyataan/' . $imagesuratpernyataan),
        );
    }

    protected function imageakrekampus(): Attribute
    {
        return Attribute::make(
            get: fn ($imageakrekampus) => url('/storage/imageakrekampus/' . $imageakrekampus),
        );
    }

    public function akademik()
    {
        return $this->hasOne(Akademik::class);
    }

    public function nonakademik()
    {
        return $this->hasOne(NonAkademik::class);
    }

    public function kesra()
    {
        return $this->hasOne(Kesra::class);
    }

    public function dinsos()
    {
        return $this->hasOne(Dinsos::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getPermissionArray()
    {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
