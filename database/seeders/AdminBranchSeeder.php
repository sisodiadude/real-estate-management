<?php

namespace Database\Seeders;

use App\Models\AdminBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Los Angeles Branch',
                'description' => 'Main branch in Los Angeles',
                'email' => 'la_branch@example.com',
                'mobile' => '1234567890',
                'address_line1' => '123 Main Street',
                'city_id' => 1,
                'state_id' => 1,
                'country_id' => 1,
                'postal_code' => '90001',
                'latitude' => 34.0522,
                'longitude' => -118.2437,
                'status' => 'active',
                'created_by_id' => 1,
                'created_by_type' => 'App\\Models\\Admin',
            ],
            [
                'name' => 'Mumbai Branch',
                'description' => 'Main branch in Mumbai',
                'email' => 'mum_branch@example.com',
                'mobile' => '1234567891',
                'address_line1' => '456 Business Street',
                'city_id' => 2,
                'state_id' => 2,
                'country_id' => 2,
                'postal_code' => '400001',
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'status' => 'active',
                'created_by_id' => 1,
                'created_by_type' => 'App\\Models\\Admin',
            ]
        ];

        foreach ($branches as $branch) {
            AdminBranch::create($branch);
        }
    }
}
