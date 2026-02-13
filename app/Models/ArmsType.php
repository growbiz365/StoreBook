<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArmsType extends Model
{
    protected $fillable = [
        'arm_type',
        'business_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function setArmTypeAttribute($value): void
    {
        $this->attributes['arm_type'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getArmTypeAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
