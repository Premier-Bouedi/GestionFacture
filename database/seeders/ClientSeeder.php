<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            Client::create([
                'name' => "Client Furtif #" . $i . " " . Str::random(5),
                'email' => "client" . $i . "_" . Str::random(3) . "@exemple.com",
            ]);
        }
    }
}
