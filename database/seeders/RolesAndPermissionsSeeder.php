<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'super-admin' => 'Super Administrator',
            'admin' => 'System Administrator',
            'manager' => 'Team Manager',
            'member' => 'Team Member',
            'viewer' => 'Viewer',
        ];

        foreach ($roles as $slug => $name) {
            Role::create([
                'name' => $name,
                'slug' => $slug,
                'description' => "Role for $name",
            ]);
        }

        // Create permissions
        $permissions = [
            // User management
            'manage-users' => 'Manage Users',
            'view-users' => 'View Users',
            'manage-roles' => 'Manage Roles and Permissions',
            
            // Team management
            'manage-teams' => 'Manage Teams',
            'view-teams' => 'View Teams',
            
            // OKR management
            'manage-objectives' => 'Manage Objectives',
            'view-objectives' => 'View Objectives',
            'view-all-objectives' => 'View All Objectives',
            'manage-key-results' => 'Manage Key Results',
            'view-key-results' => 'View Key Results',
            
            // Task management
            'manage-tasks' => 'Manage Tasks',
            'view-tasks' => 'View Tasks',
            'view-all-tasks' => 'View All Tasks',
            'accept-tasks' => 'Accept/Reject Tasks',
            'assign-tasks' => 'Assign Tasks to Anyone',
            
            // Category management
            'manage-categories' => 'Manage Categories',
            'view-categories' => 'View Categories',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::create([
                'name' => $name,
                'slug' => $slug,
                'description' => "Permission to $name",
            ]);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $manager = Role::where('slug', 'manager')->first();
        $member = Role::where('slug', 'member')->first();
        $viewer = Role::where('slug', 'viewer')->first();

        // Super Admin gets all permissions
        $superAdmin->permissions()->attach(Permission::all());

        // Admin gets all permissions except manage roles
        $admin->permissions()->attach(Permission::where('slug', '!=', 'manage-roles')->get());

        // Manager permissions
        $manager->permissions()->attach(Permission::whereIn('slug', [
            'view-users',
            'manage-teams',
            'view-teams',
            'manage-objectives',
            'view-objectives',
            'manage-key-results',
            'view-key-results',
            'manage-tasks',
            'view-tasks',
            'accept-tasks',
            'manage-categories',
            'view-categories',
        ])->get());

        // Member permissions
        $member->permissions()->attach(Permission::whereIn('slug', [
            'view-users',
            'view-teams',
            'view-objectives',
            'view-key-results',
            'view-tasks',
            'accept-tasks',
            'view-categories',
        ])->get());

        // Viewer permissions
        $viewer->permissions()->attach(Permission::whereIn('slug', [
            'view-users',
            'view-teams',
            'view-objectives',
            'view-key-results',
            'view-tasks',
            'view-categories',
        ])->get());
    }
}
