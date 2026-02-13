<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_suspended',
        'suspended_at',
        'suspended_by',
        'suspension_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Get the businesses that belong to the user.
     */
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_user', 'user_id', "business_id");
    }

    public function subUsers()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // Define the relationship to get the parent user
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if the user has Super Admin role
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Automatically assign all businesses if user is Super Admin
     */
    public function syncBusinessesForRole()
    {
        if ($this->isSuperAdmin()) {
            $allBusinesses = Business::pluck('id')->toArray();
            $this->businesses()->sync($allBusinesses);
        }
    }

    /**
     * Get all permissions that this user can assign to new roles
     * This includes ONLY permissions from their own roles (not inherited from parent)
     */
    public function getAssignablePermissions()
    {
        // Super Admin can assign all permissions
        if ($this->isSuperAdmin()) {
            return Permission::all();
        }
        
        $permissions = collect();
        
        // Get permissions from user's own roles only
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        
        // Remove duplicates and return unique permissions
        return $permissions->unique('id');
    }

    /**
     * Get permission names that this user can assign to new roles
     */
    public function getAssignablePermissionNames()
    {
        return $this->getAssignablePermissions()->pluck('name')->toArray();
    }

    /**
     * Check if user can assign a specific permission to a role
     */
    public function canAssignPermission($permissionName)
    {
        $assignablePermissions = $this->getAssignablePermissionNames();
        return in_array($permissionName, $assignablePermissions);
    }

    /**
     * Get roles that this user can assign to subusers
     * Includes their own roles and roles they created
     */
    public function getAssignableRoles()
    {
        $roles = collect();
        
        // Add user's own roles
        $roles = $roles->merge($this->roles);
        
        // Add roles created by this user
        $createdRoles = Role::createdBy($this->id)->get();
        $roles = $roles->merge($createdRoles);
        
        // If user has a parent, also include parent's roles
        if ($this->parent) {
            $roles = $roles->merge($this->parent->roles);
        }
        
        // Remove duplicates and return unique roles
        return $roles->unique('id');
    }

    /**
     * Check if user can create roles
     */
    public function canCreateRoles()
    {
        return $this->hasPermissionTo('create roles') || $this->isSuperAdmin();
    }

    /**
     * Check if user can edit a specific role
     */
    public function canEditRole($role)
    {
        // Super Admin can edit any role
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // User can edit roles they created
        if ($role->created_by == $this->id) {
            return true;
        }
        
        // User can edit roles they have
        return $this->hasRole($role);
    }

    /**
     * Suspend the user
     */
    public function suspend($reason = null, $suspendedBy = null)
    {
        $this->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspended_by' => $suspendedBy ?? auth()->id(),
            'suspension_reason' => $reason,
        ]);
    }

    /**
     * Unsuspend the user
     */
    public function unsuspend()
    {
        $this->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspended_by' => null,
            'suspension_reason' => null,
        ]);
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended()
    {
        return $this->is_suspended;
    }

    /**
     * Get the user who suspended this user
     */
    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    /**
     * Scope to get only active (non-suspended) users
     */
    public function scopeActive($query)
    {
        return $query->where('is_suspended', false);
    }

    /**
     * Scope to get only suspended users
     */
    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }
}