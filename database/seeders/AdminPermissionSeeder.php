<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'group' => 'Admin Branch',
                'actions' => [
                    'create' => 'Create a new admin branch',
                    'view_all' => 'View all admin branches',
                    'view' => 'View a specific admin branch',
                    'edit' => 'Edit an admin branch',
                    'soft_delete' => 'Soft delete an admin branch',
                    'view_all_trashed' => 'View all soft-deleted admin branches',
                    'restore_trashed' => 'Restore a soft-deleted admin branch',
                    'permanent_delete' => 'Permanently delete an admin branch',
                ]
            ],
            [
                'group' => 'Admin Department',
                'actions' => [
                    'create' => 'Create a new admin department',
                    'view_all' => 'View all admin departments',
                    'view' => 'View a specific admin department',
                    'edit' => 'Edit an admin department',
                    'soft_delete' => 'Soft delete an admin department',
                    'view_all_trashed' => 'View all soft-deleted admin departments',
                    'restore_trashed' => 'Restore a soft-deleted admin department',
                    'permanent_delete' => 'Permanently delete an admin department',
                ]
            ],
            [
                'group' => 'Admin Team',
                'actions' => [
                    'create' => 'Create a new admin team',
                    'view_all' => 'View all admin teams',
                    'view' => 'View a specific admin team',
                    'edit' => 'Edit an admin team',
                    'soft_delete' => 'Soft delete an admin team',
                    'view_all_trashed' => 'View all soft-deleted admin teams',
                    'restore_trashed' => 'Restore a soft-deleted admin team',
                    'permanent_delete' => 'Permanently delete an admin team',
                ]
            ],
            [
                'group' => 'Admin Employee',
                'actions' => [
                    'create' => 'Create a new admin employee',
                    'view_all' => 'View all admin employees',
                    'view' => 'View a specific admin employee',
                    'edit' => 'Edit an admin employee',
                    'soft_delete' => 'Soft delete an admin employee',
                    'view_all_trashed' => 'View all soft-deleted admin employees',
                    'restore_trashed' => 'Restore a soft-deleted admin employee',
                    'permanent_delete' => 'Permanently delete an admin employee',
                ]
            ]
        ];

        foreach ($permissions as $permissionGroup) {
            foreach ($permissionGroup['actions'] as $action => $description) {
                AdminPermission::create([
                    'group' => $permissionGroup['group'],
                    'action' => $action,
                    'description' => $description,
                    'is_active' => true,
                ]);
            }
        }
    }
}
