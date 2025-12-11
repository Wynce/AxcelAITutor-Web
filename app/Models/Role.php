<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get admins with this role
     */
    public function admins()
    {
        return $this->hasMany(\App\Admin::class);
    }

    /**
     * Get permissions for this role
     */
    public function rolePermissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission($permissionSlug)
    {
        if ($this->slug === 'super-admin') {
            return true; // Super admin has all permissions
        }

        return $this->rolePermissions()->where('slug', $permissionSlug)->exists();
    }
}

