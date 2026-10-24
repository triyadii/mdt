<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaMessage extends Model
{
    protected $fillable = [
        'wa_chat_id',
        'number',
        'message',
        'direction',
        'status',
        'error_message',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(WaChat::class, 'wa_chat_id');
    }
}
