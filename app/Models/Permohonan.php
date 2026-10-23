<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permohonan extends Model
{
    protected $fillable = [
        'uuid',
        'namaPermohonan',
        'nomorTelepon',
        'jenisJasa',
        'keteranganPermohonan',
        'tanggal_konsultasi',
        'jam_konsultasi',
        'status',
        'telegram_message_id',
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
