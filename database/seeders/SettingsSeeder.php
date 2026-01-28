<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        // Create default settings
        Setting::create([
            'app_name' => 'RideShare',
            'contact_email' => 'admin@rideshare.com',
            'gst_percentage' => 18.00,
            'rounding_rules' => 'nearest',
            'surcharge_configuration' => json_encode([
                'peak_hours' => 1.2,
                'late_night' => 1.5,
                'weekend' => 1.1
            ])
        ]);

        // Create default permissions
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'module' => 'Dashboard'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'module' => 'Users'],
            ['name' => 'Manage Drivers', 'slug' => 'manage-drivers', 'module' => 'Drivers'],
            ['name' => 'Manage Rides', 'slug' => 'manage-rides', 'module' => 'Rides'],
            ['name' => 'Manage Fare & Promo', 'slug' => 'manage-fare-promo', 'module' => 'Fare'],
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'module' => 'Settings'],
            ['name' => 'Manage Payments', 'slug' => 'manage-payments', 'module' => 'Payments'],
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'Reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create default roles
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Has all permissions',
            'is_default' => true
        ]);

        $admin = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Has most permissions except settings'
        ]);

        $operator = Role::create([
            'name' => 'Operator',
            'slug' => 'operator',
            'description' => 'Can manage rides and users'
        ]);

        // Assign all permissions to super admin
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        // Assign permissions to admin (all except settings)
        $adminPermissions = Permission::where('slug', '!=', 'manage-settings')->pluck('id');
        $admin->permissions()->sync($adminPermissions);

        // Assign limited permissions to operator
        $operatorPermissions = Permission::whereIn('slug', ['view-dashboard', 'manage-rides', 'view-reports'])->pluck('id');
        $operator->permissions()->sync($operatorPermissions);
    }
}