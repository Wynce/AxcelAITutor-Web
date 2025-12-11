<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Get all roles
     */
    public function getAllRoles()
    {
        return Role::where('is_active', true)->get();
    }

    /**
     * Create a new role
     */
    public function createRole($data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? \Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Attach permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->rolePermissions()->sync($data['permissions']);
            }

            return $role;
        });
    }

    /**
     * Update a role
     */
    public function updateRole($roleId, $data)
    {
        return DB::transaction(function () use ($roleId, $data) {
            $role = Role::findOrFail($roleId);
            
            $role->update([
                'name' => $data['name'] ?? $role->name,
                'slug' => $data['slug'] ?? $role->slug,
                'description' => $data['description'] ?? $role->description,
                'is_active' => $data['is_active'] ?? $role->is_active,
            ]);

            // Update permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->rolePermissions()->sync($data['permissions']);
            }

            return $role;
        });
    }

    /**
     * Delete a role
     */
    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        // Prevent deleting super-admin role
        if ($role->slug === 'super-admin') {
            throw new \Exception('Cannot delete super-admin role.');
        }

        // Check if any admins are using this role
        if ($role->admins()->count() > 0) {
            throw new \Exception('Cannot delete role that is assigned to admins.');
        }

        return $role->delete();
    }

    /**
     * Get all permissions grouped by group
     */
    public function getPermissionsGrouped()
    {
        return Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions()
    {
        return Permission::orderBy('group')->orderBy('name')->get();
    }
}

