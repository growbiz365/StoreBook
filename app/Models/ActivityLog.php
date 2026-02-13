<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper method to log activity
    public static function log(array $data)
    {
        return static::create(array_merge([
            'business_id' => session('active_business'),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $data));
    }

    // Accessor for formatted description
    public function getFormattedDescriptionAttribute(): string
    {
        return ucfirst($this->description);
    }
}
