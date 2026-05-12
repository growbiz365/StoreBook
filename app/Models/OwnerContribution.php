<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OwnerContribution extends Model
{
    public const VOUCHER_TYPE = 'Owner Contribution';

    protected $fillable = [
        'business_id',
        'contribution_via',
        'bank_id',
        'deposit_account_id',
        'from_account_id',
        'amount',
        'contribution_date',
        'received_via',
        'reference_number',
        'description',
        'created_by',
    ];

    protected $casts = [
        'contribution_date' => 'date',
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

    public function depositAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'deposit_account_id');
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OwnerContributionAttachment::class);
    }

    public function scopeForBusiness($query, ?int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $businessId = session('active_business');
        if (! $businessId) {
            abort(403);
        }

        return $this->where($field ?: $this->getRouteKeyName(), $value)
            ->where('business_id', $businessId)
            ->firstOrFail();
    }
}
