<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'username' => 'sisodiadude',
                'first_name' => 'Rohit',
                'last_name' => 'Sisodia',
                'designation' => 'Owner',
                'email' => 'dudesisodia@gmail.com',
                'email_verified_at' => now(),
                'mobile' => '7404113227',
                'mobile_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'is_verified' => true,
                'is_two_factor_enabled' => false,
                'account_status' => 'active',
                'created_by_id' => null,
                'updated_by_id' => null,
                'notification_preferences' => json_encode(['email' => true, 'sms' => false]),
                'social_links' => json_encode(['facebook' => 'https://facebook.com/admin1', 'twitter' => 'https://twitter.com/admin1']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ShekherKaushik',
                'first_name' => 'Shekher',
                'last_name' => 'Kaushik',
                'designation' => 'Owner',
                'email' => 'kaushik@gmail.com',
                'email_verified_at' => now(),
                'mobile' => '7252082288',
                'mobile_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'is_verified' => true,
                'is_two_factor_enabled' => true,
                'account_status' => 'active',
                'created_by_id' => 1,
                'updated_by_id' => 1,
                'notification_preferences' => json_encode(['email' => true, 'sms' => true]),
                'social_links' => json_encode(['linkedin' => 'https://linkedin.com/in/admin2']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Admin::insert($admins);
    }
}
