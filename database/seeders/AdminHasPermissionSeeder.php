<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminHasPermission;
use App\Models\AdminPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = Admin::all(); // Fetch all admins

        if ($admins->isEmpty()) {
            return;
        }

        $permissions = AdminPermission::all();

        foreach ($admins as $admin) {
            foreach ($permissions as $permission) {
                AdminHasPermission::create([
                    'permission_id' => $permission->id,
                    'admin_id' => $admin->id,
                    'created_by' => $admin->id, // Assign creator as the admin itself
                    'updated_by' => $admin->id,
                ]);
            }
        }
    }
}
