<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OwnerContributionAttachment extends Model
{
    protected $fillable = [
        'owner_contribution_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_extension',
        'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (OwnerContributionAttachment $attachment): void {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        });
    }

    public function ownerContribution(): BelongsTo
    {
        return $this->belongsTo(OwnerContribution::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $attachment = static::where($field ?: $this->getRouteKeyName(), $value)->firstOrFail();
        $businessId = session('active_business');
        $contribution = $attachment->ownerContribution;
        if (! $businessId || ! $contribution || (int) $contribution->business_id !== (int) $businessId) {
            abort(404);
        }

        return $attachment;
    }
}
