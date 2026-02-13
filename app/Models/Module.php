<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'guard_name', 'code'];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_module', 'module_id', 'package_id');
    }
}
