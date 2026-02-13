<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartyTransfer extends Model
{
    protected $fillable = [
        'business_id',
        'date',
        'debit_party_id',
        'credit_party_id',
        'transfer_amount',
        'details',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'transfer_amount' => 'decimal:2'
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function debitParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'debit_party_id');
    }

    public function creditParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'credit_party_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PartyTransferAttachment::class);
    }
} 