<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArmsMake extends Model
{
    protected $fillable = [
        'arm_make',
        'business_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function setArmMakeAttribute($value): void
    {
        $this->attributes['arm_make'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getArmMakeAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
