<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        $managers = [
            [
                'name' => 'Sara Gestion',
                'email' => 'sara@facplus.com',
                'role' => 'manager',
            ],
            [
                'name' => 'Hassan Comptable',
                'email' => 'hassan@facplus.com',
                'role' => 'manager',
            ],
            [
                'name' => 'Yassine Stock',
                'email' => 'yassine@facplus.com',
                'role' => 'manager',
            ],
            [
                'name' => 'Laila Admin',
                'email' => 'laila@facplus.com',
                'role' => 'admin',
            ],
        ];

        foreach ($managers as $manager) {
            User::updateOrCreate(
                ['email' => $manager['email']],
                [
                    'name' => $manager['name'],
                    'password' => Hash::make('manager2026'),
                    'role' => $manager['role'],
                ]
            );
        }
    }
}
