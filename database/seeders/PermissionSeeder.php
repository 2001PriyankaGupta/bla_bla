<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\AdminUser;
use App\Models\User; // Add this if you're creating regular users
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('admin_users')->delete();
        DB::table('permissions')->delete();

        $permissions = [
            // Dashboard Permissions
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'module' => 'Dashboard'],
            
            // User Management Permissions
            ['name' => 'View Users', 'slug' => 'view-users', 'module' => 'User Management'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'module' => 'User Management'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'module' => 'User Management'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'module' => 'User Management'],
            ['name' => 'Export Users', 'slug' => 'export-users', 'module' => 'User Management'],
            
            // Ride Management Permissions
            ['name' => 'View Rides', 'slug' => 'view-rides', 'module' => 'Ride Management'],
            ['name' => 'Create Rides', 'slug' => 'create-rides', 'module' => 'Ride Management'],
            ['name' => 'Edit Rides', 'slug' => 'edit-rides', 'module' => 'Ride Management'],
            ['name' => 'Delete Rides', 'slug' => 'delete-rides', 'module' => 'Ride Management'],
            ['name' => 'Approve Rides', 'slug' => 'approve-rides', 'module' => 'Ride Management'],
            ['name' => 'Cancel Rides', 'slug' => 'cancel-rides', 'module' => 'Ride Management'],
            
            // Fare Management Permissions
            ['name' => 'View Fare Promo', 'slug' => 'view-fare-promo', 'module' => 'Fare Management'],
            ['name' => 'Create Fare Promo', 'slug' => 'create-fare-promo', 'module' => 'Fare Management'],
            ['name' => 'Edit Fare Promo', 'slug' => 'edit-fare-promo', 'module' => 'Fare Management'],
            ['name' => 'Delete Fare Promo', 'slug' => 'delete-fare-promo', 'module' => 'Fare Management'],
            ['name' => 'Apply Fare Promo', 'slug' => 'apply-fare-promo', 'module' => 'Fare Management'],
            
            // Payment Management Permissions
            ['name' => 'View Payments', 'slug' => 'view-payments', 'module' => 'Payment Management'],
            ['name' => 'Manage Payments', 'slug' => 'manage-payments', 'module' => 'Payment Management'],
            ['name' => 'Process Refunds', 'slug' => 'process-refunds', 'module' => 'Payment Management'],
            ['name' => 'View Transactions', 'slug' => 'view-transactions', 'module' => 'Payment Management'],
            ['name' => 'Export Payments', 'slug' => 'export-payments', 'module' => 'Payment Management'],
            
            // Support Management Permissions
            ['name' => 'View Support', 'slug' => 'view-support', 'module' => 'Support Management'],
            ['name' => 'Manage Support', 'slug' => 'manage-support', 'module' => 'Support Management'],
            ['name' => 'Reply Tickets', 'slug' => 'reply-tickets', 'module' => 'Support Management'],
            ['name' => 'Close Tickets', 'slug' => 'close-tickets', 'module' => 'Support Management'],
            ['name' => 'Escalate Tickets', 'slug' => 'escalate-tickets', 'module' => 'Support Management'],
            
            // Settings Permissions
            ['name' => 'View Settings', 'slug' => 'view-settings', 'module' => 'Settings'],
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'module' => 'Settings'],
            ['name' => 'Manage API Keys', 'slug' => 'manage-api-keys', 'module' => 'Settings'],
            ['name' => 'Manage Taxes', 'slug' => 'manage-taxes', 'module' => 'Settings'],
            ['name' => 'Manage Admin Users', 'slug' => 'manage-admin-users', 'module' => 'Settings'],
            
            // Reports Permissions
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'Reports'],
            ['name' => 'Generate Reports', 'slug' => 'generate-reports', 'module' => 'Reports'],
            ['name' => 'Export Reports', 'slug' => 'export-reports', 'module' => 'Reports'],
            ['name' => 'View Analytics', 'slug' => 'view-analytics', 'module' => 'Reports'],
            
            // Notifications Permissions
            ['name' => 'View Notifications', 'slug' => 'view-notifications', 'module' => 'Notifications'],
            ['name' => 'Send Notifications', 'slug' => 'send-notifications', 'module' => 'Notifications'],
            ['name' => 'Manage Templates', 'slug' => 'manage-templates', 'module' => 'Notifications'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Super Admin User with all permissions
        $allPermissionSlugs = array_column($permissions, 'slug');
        
        $superAdmin = AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
            'permissions' => $allPermissionSlugs
        ]);

        // Create Sample Admin Users with different permission sets
        
        // Manager User - Most permissions except sensitive ones
        $managerPermissions = [
            'view-dashboard',
            'view-users', 'edit-users',
            'view-rides', 'edit-rides', 'approve-rides', 'cancel-rides',
            'view-fare-promo', 'edit-fare-promo', 'apply-fare-promo',
            'view-payments', 'view-transactions',
            'view-support', 'reply-tickets', 'close-tickets',
            'view-settings',
            'view-reports', 'view-analytics',
            'view-notifications'
        ];
        
        AdminUser::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'permissions' => $managerPermissions
        ]);

        // Support User - Limited to support functions
        $supportPermissions = [
            'view-dashboard',
            'view-users',
            'view-rides',
            'view-support', 'reply-tickets', 'close-tickets',
            'view-notifications'
        ];
        
        AdminUser::create([
            'name' => 'Support User',
            'email' => 'support@example.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'permissions' => $supportPermissions
        ]);

        // View Only User - Can only view data
        $viewOnlyPermissions = [
            'view-dashboard',
            'view-users',
            'view-rides',
            'view-fare-promo',
            'view-payments',
            'view-support',
            'view-reports',
            'view-analytics'
        ];
        
        AdminUser::create([
            'name' => 'View Only User',
            'email' => 'viewonly@example.com',
            'password' => Hash::make('password'),
            'is_super_admin' => false,
            'permissions' => $viewOnlyPermissions
        ]);

        $this->command->info('✅ Permissions seeded successfully!');
        $this->command->info('📊 Total permissions created: ' . count($permissions));
        $this->command->info('👥 Admin users created:');
        $this->command->info('   - Super Admin: superadmin@example.com / password');
        $this->command->info('   - Manager: manager@example.com / password');
        $this->command->info('   - Support: support@example.com / password');
        $this->command->info('   - View Only: viewonly@example.com / password');
    }
}