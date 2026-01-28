<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $adminUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
                'permissions' => ['all'] // Has all permissions
            ],
            [
                'name' => 'Admin User 1',
                'email' => 'admin1@example.com',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'permissions' => ['view-dashboard', 'view-users', 'view-rides']
            ],
            [
                'name' => 'Admin User 2', 
                'email' => 'admin2@example.com',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'permissions' => ['view-dashboard', 'view-fare-promo', 'view-payments']
            ]
        ];

        foreach ($adminUsers as $user) {
            AdminUser::create($user);
        }

        $this->command->info('✅ Admin users seeded successfully!');
    }
}