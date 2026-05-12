<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OwnerDrawingAttachment extends Model
{
    protected $fillable = [
        'owner_drawing_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_extension',
        'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (OwnerDrawingAttachment $attachment): void {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        });
    }

    public function ownerDrawing(): BelongsTo
    {
        return $this->belongsTo(OwnerDrawing::class);
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
        $drawing = $attachment->ownerDrawing;
        if (! $businessId || ! $drawing || (int) $drawing->business_id !== (int) $businessId) {
            abort(404);
        }

        return $attachment;
    }
}
