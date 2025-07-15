<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'make:super-admin {email : The email of the user to make super admin}';
    protected $description = 'Make a user a super administrator';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");
            return 1;
        }

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if (!$superAdminRole) {
            $this->error('Super Admin role not found. Please run database seeders first.');
            return 1;
        }

        $user->roles()->sync([$superAdminRole->id]);
        $this->info("Successfully made {$user->name} a super administrator!");

        return 0;
    }
} 