<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Bank extends Model
{
    protected $fillable = [
        'business_id',
        'account_type',
        'account_name',
        'bank_name',
        'description',
        'opening_balance',
        'chart_of_account_id',
        'user_id',
        'status'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'status' => 'boolean'
    ];

    public function setAccountNameAttribute($value): void
    {
        $this->attributes['account_name'] = $value !== null ? Str::upper($value) : $value;
    }

    public function setBankNameAttribute($value): void
    {
        $this->attributes['bank_name'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getAccountNameAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function getBankNameAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(BankLedger::class);
    }

    /**
     * Get the current balance for this bank
     */
    public function getBalance($asOfDate = null): float
    {
        $query = $this->ledgerEntries()
            ->where('business_id', session('active_business'));
        
        // If a date is provided, filter entries up to that date
        if ($asOfDate) {
            $query->where('date', '<=', $asOfDate);
        }
        
        $balance = $query->get()
            ->reduce(function ($carry, $entry) {
                return $carry + $entry->deposit_amount - $entry->withdrawal_amount;
            }, 0);
        
        return $balance;
    }

    /**
     * Get formatted balance with sign
     */
    public function getFormattedBalance($asOfDate = null): string
    {
        $balance = $this->getBalance($asOfDate);
        $sign = $balance >= 0 ? '+' : '';
        return $sign . number_format($balance, 2);
    }

    /**
     * Get balance status (positive, negative, or zero)
     */
    public function getBalanceStatus($asOfDate = null): string
    {
        $balance = $this->getBalance($asOfDate);
        if ($balance > 0) {
            return 'positive';
        } elseif ($balance < 0) {
            return 'negative';
        }
        return 'zero';
    }
} 