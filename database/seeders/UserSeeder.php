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
        $adminRole = Role::where('name', 'Admin')->first();

        User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@labrpl.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => true,
        ]);
    }
}
