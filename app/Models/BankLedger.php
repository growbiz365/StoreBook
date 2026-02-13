<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankLedger extends Model
{
    protected $table = 'bank_ledger';

    protected $fillable = [
        'business_id',
        'bank_id',
        'date',
        'deposit_amount',
        'withdrawal_amount',
        'voucher_type',
        'voucher_id',
        'details',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'deposit_amount' => 'decimal:2',
        'withdrawal_amount' => 'decimal:2'
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 