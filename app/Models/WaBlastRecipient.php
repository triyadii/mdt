<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaBlastRecipient extends Model
{
    protected $fillable = [
        'campaign_id',
        'recipient_name',
        'phone_number',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(WaBlastCampaign::class, 'campaign_id');
    }
}
