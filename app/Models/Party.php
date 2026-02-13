<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Party extends Model
{
    protected $fillable = [
        'business_id',
        'chart_of_account_id',
        'name',
        'address',
        'phone_no',
        'whatsapp_no',
        'cnic',
        'ntn',
        'opening_balance',
        'opening_date',
        'opening_type',
        'user_id',
        'status'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_date' => 'date',
        'status' => 'boolean'
    ];

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getNameAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(PartyLedger::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(PartyLedger::class, 'party_id', 'id');
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    /**
     * Get the current balance for this party
     */
    public function getBalance($asOfDate = null): float
    {
        $query = $this->ledgerEntries()
            ->where('business_id', session('active_business'));
        
        // If a date is provided, filter entries up to that date
        if ($asOfDate) {
            $query->where('date_added', '<=', $asOfDate);
        }
        
        $balance = $query->get()
            ->reduce(function ($carry, $entry) {
                return $carry + $entry->credit_amount - $entry->debit_amount;
            }, 0);
        
        // Note: Opening balance should be handled as a ledger entry, not added separately
        // This ensures consistency with other modules that use withSum
        
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