<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Jasa extends Model
{
    protected $fillable = [
        'uuid',
        'namaJasa',
        'keteranganJasa',
        'harga_mulai',
        'estimasi_waktu',
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
