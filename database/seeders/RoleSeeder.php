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
                'name' => 'Super Admin',
                'display_name' => 'Super Administrator',
                'description' => 'Akses penuh ke semua fitur sistem termasuk manajemen peran dan pengguna.',
            ],
            [
                'name' => 'Admin',
                'display_name' => 'Administrator',
                'description' => 'Akses ke semua fitur praktikum kecuali manajemen peran dan pengguna.',
            ],
            [
                'name' => 'Aslab',
                'display_name' => 'Asisten Laboratorium',
                'description' => 'Asisten Laboratorium untuk mengelola bimbingan dan tugas.',
            ],
            [
                'name' => 'Praktikan',
                'display_name' => 'Praktikan',
                'description' => 'Mahasiswa praktikan yang mendaftar praktikum.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
