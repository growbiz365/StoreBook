<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArmsCondition extends Model
{
    protected $fillable = [
        'arm_condition',
        'business_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function setArmConditionAttribute($value): void
    {
        $this->attributes['arm_condition'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getArmConditionAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}

