<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartyTransferAttachment extends Model
{
    protected $fillable = [
        'party_transfer_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'user_id'
    ];

    public function partyTransfer(): BelongsTo
    {
        return $this->belongsTo(PartyTransfer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 