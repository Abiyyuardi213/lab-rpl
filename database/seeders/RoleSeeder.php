<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'role_name' => 'Admin',
                'role_description' => 'Administrator with full access',
                'role_status' => true,
            ],
            [
                'role_name' => 'Aslab',
                'role_description' => 'Asisten Laboratorium',
                'role_status' => true,
            ],
            [
                'role_name' => 'Praktikan',
                'role_description' => 'User Praktikan',
                'role_status' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::createRole($role);
        }
    }
}
