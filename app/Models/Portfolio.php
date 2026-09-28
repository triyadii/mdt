<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    protected $fillable = [
        'uuid',
        'nama_project',
        'jenis_project',
        'keterangan_project',
        'link_project',
        'gambar',
    ];

    protected $casts = [
        'gambar' => 'array',
    ];

    public function type()
    {
        return $this->belongsTo(ProjectType::class, 'jenis_project', 'uuid');
    }

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
