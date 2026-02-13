<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = ['package_name', 'description', 'price', 'currency_id', 'duration_months'];

    // Define the relationship to Currency
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Permission::class, 'package_module', 'package_id', 'module_id');
    }
}
