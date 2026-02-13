<?php
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'created_by'
    ];

    /**
     * Get the user who created this role
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get roles created by a specific user
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope to get system roles (not created by any user)
     */
    public function scopeSystemRoles($query)
    {
        return $query->whereNull('created_by');
    }
}