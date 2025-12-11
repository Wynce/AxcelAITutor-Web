<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view user list', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'description' => 'Can create new users', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'description' => 'Can edit user information', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users', 'group' => 'users'],
            ['name' => 'Change User Status', 'slug' => 'change-user-status', 'description' => 'Can activate/deactivate users', 'group' => 'users'],

            // Admin Management
            ['name' => 'View Admins', 'slug' => 'view-admins', 'description' => 'Can view admin list', 'group' => 'admins'],
            ['name' => 'Create Admins', 'slug' => 'create-admins', 'description' => 'Can create new admins', 'group' => 'admins'],
            ['name' => 'Edit Admins', 'slug' => 'edit-admins', 'description' => 'Can edit admin information', 'group' => 'admins'],
            ['name' => 'Delete Admins', 'slug' => 'delete-admins', 'description' => 'Can delete admins', 'group' => 'admins'],
            ['name' => 'Change Admin Status', 'slug' => 'change-admin-status', 'description' => 'Can activate/deactivate admins', 'group' => 'admins'],

            // Role Management
            ['name' => 'View Roles', 'slug' => 'view-roles', 'description' => 'Can view roles list', 'group' => 'roles'],
            ['name' => 'Create Roles', 'slug' => 'create-roles', 'description' => 'Can create new roles', 'group' => 'roles'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles', 'description' => 'Can edit roles', 'group' => 'roles'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles', 'description' => 'Can delete roles', 'group' => 'roles'],

            // Settings
            ['name' => 'View Settings', 'slug' => 'view-settings', 'description' => 'Can view settings', 'group' => 'settings'],
            ['name' => 'Edit Settings', 'slug' => 'edit-settings', 'description' => 'Can edit settings', 'group' => 'settings'],

            // Notifications
            ['name' => 'View Notifications', 'slug' => 'view-notifications', 'description' => 'Can view notifications', 'group' => 'notifications'],
            ['name' => 'Send Notifications', 'slug' => 'send-notifications', 'description' => 'Can send notifications', 'group' => 'notifications'],

            // Chat Management
            ['name' => 'View Chat History', 'slug' => 'view-chat-history', 'description' => 'Can view chat history', 'group' => 'chats'],
            ['name' => 'Manage Chatbots', 'slug' => 'manage-chatbots', 'description' => 'Can manage chatbots', 'group' => 'chats'],

            // Activity Logs
            ['name' => 'View Activity Logs', 'slug' => 'view-activity-logs', 'description' => 'Can view activity logs', 'group' => 'logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}

