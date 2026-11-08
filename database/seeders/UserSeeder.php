<?php

namespace Database\Seeders;

use App\Models\Aslab;
use App\Models\Praktikan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $aslabRole = Role::where('name', 'Aslab')->first();
        $praktikanRole = Role::where('name', 'Praktikan')->first();

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

        // 10 Asisten Laboratorium (Aslab)
        $aslabsData = [
            [
                'npm' => '06.2022.1.07001',
                'name' => 'Adhitya Putra Pratama',
                'email' => 'aslab.adhitya@labrpl.com',
                'jabatan' => 'Koordinator Laboratorium',
                'angkatan' => '2022',
                'no_hp' => '081234567001',
                'bio' => 'Fokus pada arsitektur sistem backend dan devops.',
                'skills' => ['Laravel', 'Docker', 'MySQL'],
            ],
            [
                'npm' => '06.2022.1.07002',
                'name' => 'Bima Arya Wicaksana',
                'email' => 'aslab.bima@labrpl.com',
                'jabatan' => 'Koordinator Praktikum',
                'angkatan' => '2022',
                'no_hp' => '081234567002',
                'bio' => 'Pengembang fullstack dan optimasi query basis data.',
                'skills' => ['PHP', 'Vue.js', 'PostgreSQL'],
            ],
            [
                'npm' => '06.2022.1.07003',
                'name' => 'Cindy Aulia Putri',
                'email' => 'aslab.cindy@labrpl.com',
                'jabatan' => 'Sekretaris Laboratorium',
                'angkatan' => '2022',
                'no_hp' => '081234567003',
                'bio' => 'Manajemen administrasi praktikum dan dokumentasi sistem.',
                'skills' => ['System Analysis', 'Laravel', 'UI/UX'],
            ],
            [
                'npm' => '06.2022.1.07004',
                'name' => 'Dimas Rizky Nugroho',
                'email' => 'aslab.dimas@labrpl.com',
                'jabatan' => 'Bendahara Laboratorium',
                'angkatan' => '2022',
                'no_hp' => '081234567004',
                'bio' => 'Pengembangan modul praktikum dan logistik lab.',
                'skills' => ['PHP', 'CodeIgniter', 'MySQL'],
            ],
            [
                'npm' => '06.2023.1.07005',
                'name' => 'Erlangga Dwi Saputra',
                'email' => 'aslab.erlangga@labrpl.com',
                'jabatan' => 'Asisten Laboratorium',
                'angkatan' => '2023',
                'no_hp' => '081234567005',
                'bio' => 'Instruktur praktikum Pemrograman Terstruktur.',
                'skills' => ['C++', 'Python', 'Algorithms'],
            ],
            [
                'npm' => '06.2023.1.07006',
                'name' => 'Farhan Maulana',
                'email' => 'aslab.farhan@labrpl.com',
                'jabatan' => 'Asisten Laboratorium',
                'angkatan' => '2023',
                'no_hp' => '081234567006',
                'bio' => 'Instruktur praktikum Struktur Data.',
                'skills' => ['Java', 'C++', 'Data Structures'],
            ],
            [
                'npm' => '06.2023.1.07007',
                'name' => 'Gita Permata Sari',
                'email' => 'aslab.gita@labrpl.com',
                'jabatan' => 'Asisten Laboratorium',
                'angkatan' => '2023',
                'no_hp' => '081234567007',
                'bio' => 'Instruktur praktikum Basis Data.',
                'skills' => ['MySQL', 'Database Design', 'PHP'],
            ],
            [
                'npm' => '06.2023.1.07008',
                'name' => 'Hafizh Ramadhan',
                'email' => 'aslab.hafizh@labrpl.com',
                'jabatan' => 'Asisten Laboratorium',
                'angkatan' => '2023',
                'no_hp' => '081234567008',
                'bio' => 'Dukungan teknis jaringan dan infrastruktur laboratorium.',
                'skills' => ['Linux', 'Networking', 'Git'],
            ],
            [
                'npm' => '06.2023.1.07009',
                'name' => 'Indah Nur Cahyani',
                'email' => 'aslab.indah@labrpl.com',
                'jabatan' => 'Asisten Laboratorium',
                'angkatan' => '2023',
                'no_hp' => '081234567009',
                'bio' => 'Evaluator tugas asistensi dan manajemen nilai praktikan.',
                'skills' => ['Laravel', 'TailwindCSS', 'MySQL'],
            ],
            [
                'npm' => '06.2023.1.07010',
                'name' => 'Jafar Shadiq',
                'email' => 'aslab.jafar@labrpl.com',
                'jabatan' => 'Asisten Laboratorium',
                'angkatan' => '2023',
                'no_hp' => '081234567010',
                'bio' => 'Pengembangan antarmuka web dan pengujian perangkat lunak.',
                'skills' => ['JavaScript', 'HTML/CSS', 'PHP'],
            ],
        ];

        foreach ($aslabsData as $item) {
            $user = User::updateOrCreate(
                ['username' => $item['npm']],
                [
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => Hash::make('password'),
                    'role_id' => $aslabRole->id,
                    'status' => true,
                ]
            );

            Aslab::updateOrCreate(
                ['npm' => $item['npm']],
                [
                    'user_id' => $user->id,
                    'jabatan' => $item['jabatan'],
                    'jurusan' => 'Teknik Informatika',
                    'angkatan' => $item['angkatan'],
                    'no_hp' => $item['no_hp'],
                    'slug' => Str::slug($item['name']),
                    'bio' => $item['bio'],
                    'skills' => $item['skills'],
                ]
            );
        }

        // 20 Praktikan (Mahasiswa)
        $praktikanNames = [
            'Achmad Fauzi',
            'Bagas Prasetyo',
            'Cantika Dewi Lestari',
            'Danu Wahyudi',
            'Elsa Febrianti',
            'Fajar Hidayat',
            'Gilang Pratama',
            'Hanif Alamsyah',
            'Intan Permatasari',
            'Kevin Sanjaya',
            'Luthfi Hakim',
            'Maya Anggraini',
            'Naufal Zaki',
            'Olivia Putri',
            'Pandu Wicaksono',
            'Qori Amanda',
            'Raditya Pratama',
            'Salsa Bella',
            'Taufik Hidayat',
            'Vina Maharani',
        ];

        foreach ($praktikanNames as $index => $name) {
            $number = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $npm = "06.2024.1.08{$number}";
            $email = 'praktikan'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@labrpl.com';
            $noHp = '08571234'.str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            $user = User::updateOrCreate(
                ['username' => $npm],
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role_id' => $praktikanRole->id,
                    'status' => true,
                ]
            );

            Praktikan::updateOrCreate(
                ['npm' => $npm],
                [
                    'user_id' => $user->id,
                    'jurusan' => 'Teknik Informatika',
                    'angkatan' => '2024',
                    'no_hp' => $noHp,
                ]
            );
        }
    }
}
