<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PartyLedger extends Model
{
    protected $fillable = [
        'business_id',
        'party_id',
        'voucher_id',
        'voucher_type',
        'date_added',
        'user_id',
        'debit_amount',
        'credit_amount'
    ];

    protected $casts = [
        'date_added' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2'
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): MorphTo
    {
        return $this->morphTo('voucher', 'voucher_type', 'voucher_id');
    }

    public function partyTransfer()
    {
        return $this->belongsTo(PartyTransfer::class, 'voucher_id');
    }
} 