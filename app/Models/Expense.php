<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Expense extends Model
{
    protected $fillable = [
        'expense_head_id',
        'bank_id',
        'amount',
        'date_added',
        'details',
        'business_id',
        'user_id',
    ];

    protected $casts = [
        'date_added' => 'date',
        'amount' => 'decimal:2',
    ];

    public function expenseHead(): BelongsTo
    {
        return $this->belongsTo(ExpenseHead::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ExpenseAttachment::class);
    }

    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'voucher', 'voucher_type', 'voucher_id');
    }
}
