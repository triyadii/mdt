<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaChat extends Model
{
    protected $fillable = [
        'number',
        'name',
        'last_message',
        'last_message_at',
        'unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(WaMessage::class, 'wa_chat_id')->orderBy('created_at', 'asc');
    }
}
