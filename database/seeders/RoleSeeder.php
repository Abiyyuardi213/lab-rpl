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
                'name' => 'Admin',
                'display_name' => 'Administrator',
                'description' => 'Administrator with full access',
            ],
            [
                'name' => 'Aslab',
                'display_name' => 'Asisten Laboratorium',
                'description' => 'Asisten Laboratorium',
            ],
            [
                'name' => 'Praktikan',
                'display_name' => 'Praktikan',
                'description' => 'User Praktikan',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
