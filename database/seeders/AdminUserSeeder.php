<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'magnagamakelighiclainn@gmail.com'],
            [
                'name' => 'Claïnn Admin',
                'password' => Hash::make('password'), // Vous pourrez le changer plus tard
                'role' => 'admin',
            ]
        );
    }
}
