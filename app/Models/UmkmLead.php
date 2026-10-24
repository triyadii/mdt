<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UmkmLead extends Model
{
    protected $fillable = [
        'task_id',
        'place_id',
        'name',
        'category',
        'phone_raw',
        'phone_wa',
        'address',
        'review_count',
        'rating',
        'maps_url',
        'has_website',
        'website_url',
        'location_tag',
        'status',
        'notes',
    ];

    protected $casts = [
        'has_website' => 'boolean',
        'review_count' => 'integer',
        'rating' => 'float',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ScraperTask::class, 'task_id');
    }
}
