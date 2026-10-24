<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaBlastCampaign extends Model
{
    protected $fillable = [
        'title',
        'message',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'delay_seconds',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(WaBlastRecipient::class, 'campaign_id')->orderBy('id', 'asc');
    }

    public function pendingRecipients(): HasMany
    {
        return $this->hasMany(WaBlastRecipient::class, 'campaign_id')->where('status', 'pending')->orderBy('id', 'asc');
    }
}
