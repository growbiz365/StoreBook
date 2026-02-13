<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralVoucher extends Model
{
    protected $fillable = [
        'business_id',
        'bank_id',
        'party_id',
        'entry_type',
        'amount',
        
        'details',
        'entry_date',
        'user_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
        
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(GeneralVoucherAttachment::class);
    }
}