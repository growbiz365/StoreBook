<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Expense;

class JournalEntry extends Model
{
    protected $fillable = [
        'business_id',
        'account_head',
        'debit_amount',
        'credit_amount',
        'voucher_id',
        'voucher_type',
        'comments',
        'user_id',
        'date_added'
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

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_head');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): MorphTo
    {
        return $this->morphTo('voucher', 'voucher_type', 'voucher_id');
    }
}
