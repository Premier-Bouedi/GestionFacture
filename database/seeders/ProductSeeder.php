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
            [
                'designation' => 'Laptop Gamer Pro',
                'prix_unitaire' => 15000,
                'stock' => 5,
                'description' => 'Ordinateur portable haute performance pour le gaming.',
            ],
            [
                'designation' => 'Souris Optique',
                'prix_unitaire' => 150,
                'stock' => 100,
                'description' => 'Souris optique filaire, confortable et précise.',
            ],
            [
                'designation' => 'Clavier HS',
                'prix_unitaire' => 300,
                'stock' => 0,
                'description' => 'Clavier mécanique — rupture de stock (test blocage vente).',
            ],
            [
                'designation' => 'Écran 4K',
                'prix_unitaire' => 4500,
                'stock' => 1,
                'description' => 'Moniteur 27 pouces 4K, dernière unité disponible.',
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::updateOrCreate(
                ['designation' => $product['designation']],
                $product
            );
        }
    }
}
