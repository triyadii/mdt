<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScraperTask extends Model
{
    protected $fillable = [
        'title',
        'keyword',
        'location',
        'filter_no_website',
        'filter_has_phone',
        'min_reviews',
        'min_rating',
        'frequency',
        'status',
        'scheduled_at',
        'last_run_at',
        'leads_count',
        'progress_percent',
        'current_step',
        'eta_seconds',
        'log_message',
    ];

    protected $casts = [
        'filter_no_website' => 'boolean',
        'filter_has_phone' => 'boolean',
        'min_reviews' => 'integer',
        'min_rating' => 'float',
        'scheduled_at' => 'datetime',
        'last_run_at' => 'datetime',
        'leads_count' => 'integer',
        'progress_percent' => 'integer',
        'eta_seconds' => 'integer',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(UmkmLead::class, 'task_id');
    }
}
