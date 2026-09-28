<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyProfile extends Model
{
    protected $fillable = [
        'uuid',
        'nama_profil',
        'instagram',
        'tiktok',
        'thread',
        'email',
        'nomor_telepon',
        'alamat',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
