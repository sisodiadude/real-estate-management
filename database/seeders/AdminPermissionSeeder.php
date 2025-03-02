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
