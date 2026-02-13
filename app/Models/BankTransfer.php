<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransfer extends Model
{
    protected $fillable = [
        'business_id',
        'from_account_id',
        'to_account_id',
        'amount',
        'details',
        'transfer_date',
        'user_id',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function attachments()
    {
        return $this->hasMany(BankTransferAttachment::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'to_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}