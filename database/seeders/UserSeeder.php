<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user first, but without a company initially
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Now create the company and assign the admin as the owner
        $company = Company::create([
            'name' => 'Default Company',
            'user_id' => $admin->id,
        ]);

        // Now, update the admin user with the company_id
        $admin->company_id = $company->id;
        $admin->save();

        // Assign the role to admin
        $admin->assignRole('super-admin');

        // Create manager user
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => $company->id,
        ]);
        $manager->assignRole('manager');

        // Create team member user
        $member = User::create([
            'name' => 'Team Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => $company->id,
        ]);
        $member->assignRole('member');

        // Create viewer user
        $viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => $company->id,
        ]);
        $viewer->assignRole('viewer');
    }
} 