<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Laptop Gamer Pro', 'price' => 15000, 'stock' => 5],  // Stock limité
            ['name' => 'Souris Optique', 'price' => 150, 'stock' => 100],    // Stock large
            ['name' => 'Clavier HS', 'price' => 300, 'stock' => 0],         // TEST : Doit bloquer la vente
            ['name' => 'Écran 4K', 'price' => 4500, 'stock' => 1],          // TEST : Dernière unité
        ];

        foreach ($products as $p) {
            \App\Models\Product::create($p);
        }
    }
}
