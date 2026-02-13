<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OtherIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'income_date',
        'amount',
        'description',
        'bank_id',
        'chart_of_account_id',
    ];

    protected $casts = [
        'income_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OtherIncomeAttachment::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'voucher_id')->where('voucher_type', 'other_income');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($otherIncome) {
            $otherIncome->createJournalEntries();
        });

        static::updated(function ($otherIncome) {
            $otherIncome->updateJournalEntries();
        });

        static::deleting(function ($otherIncome) {
            $otherIncome->deleteJournalEntries();
        });
    }

    public function createJournalEntries()
    {
        // Create bank ledger entry (deposit)
        \App\Models\BankLedger::create([
            'business_id' => $this->business_id,
            'bank_id' => $this->bank_id,
            'voucher_id' => $this->id,
            'voucher_type' => 'Other Income',
            'date' => $this->income_date,
            'user_id' => $this->user_id,
            'deposit_amount' => $this->amount,
            'withdrawal_amount' => 0,
            'details' => 'Other Income: ' . $this->description,
        ]);

        // Debit Bank Account
        JournalEntry::create([
            'business_id' => $this->business_id,
            'account_head' => $this->bank->chart_of_account_id,
            'debit_amount' => $this->amount,
            'credit_amount' => 0,
            'voucher_id' => $this->id,
            'voucher_type' => 'other_income',
            'comments' => 'Other Income: ' . $this->description,
            'user_id' => $this->user_id,
            'date_added' => $this->income_date,
        ]);

        // Credit Income Account
        JournalEntry::create([
            'business_id' => $this->business_id,
            'account_head' => $this->chart_of_account_id,
            'debit_amount' => 0,
            'credit_amount' => $this->amount,
            'voucher_id' => $this->id,
            'voucher_type' => 'other_income',
            'comments' => 'Other Income: ' . $this->description,
            'user_id' => $this->user_id,
            'date_added' => $this->income_date,
        ]);
    }

    public function updateJournalEntries()
    {
        $this->deleteJournalEntries();
        $this->createJournalEntries();
    }

    public function deleteJournalEntries()
    {
        // Delete bank ledger entries
        \App\Models\BankLedger::where('voucher_id', $this->id)
            ->where('voucher_type', 'Other Income')
            ->delete();
            
        // Delete journal entries
        JournalEntry::where('voucher_id', $this->id)
            ->where('voucher_type', 'other_income')
            ->delete();
    }
}