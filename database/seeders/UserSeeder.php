<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        // Ghost - Super Admin
        User::updateOrCreate(
            ['username' => 'ghost'],
            [
                'name' => 'Ghost Super Admin',
                'email' => 'ghost@labrpl.com',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'status' => true,
            ]
        );

        // Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@labrpl.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'status' => true,
            ]
        );
        // Admin RPL (Secure)
        User::updateOrCreate(
            ['username' => 'admin_rpl'],
            [
                'name' => 'Admin RPL',
                'email' => 'admin_rpl@labrpl.com',
                'password' => Hash::make('IOnmfgoprtlkasbnsd2025l'),
                'role_id' => $adminRole->id,
                'status' => true,
            ]
        );
    }
}
