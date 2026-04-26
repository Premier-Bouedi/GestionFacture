<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer quelques produits si la table est vide
        if (Product::count() === 0) {
            $products = [
                ['name' => 'Ordinateur Portable', 'price' => 1200, 'stock' => 50],
                ['name' => 'Souris Sans Fil', 'price' => 25, 'stock' => 200],
                ['name' => 'Clavier Mécanique', 'price' => 80, 'stock' => 100],
                ['name' => 'Écran 27 Pouces', 'price' => 300, 'stock' => 30],
                ['name' => 'Casque Audio', 'price' => 150, 'stock' => 60],
            ];
            foreach ($products as $p) {
                Product::create($p);
            }
        }

        $allProducts = Product::all();
        $clients = Client::all();

        // 2. Créer une facture pour chaque client
        foreach ($clients as $client) {
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'number' => 'FACT-' . Carbon::now()->format('Ymd') . '-' . str_pad($client->id, 4, '0', STR_PAD_LEFT),
                'invoice_date' => Carbon::now()->subDays(rand(0, 30)),
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
            ]);

            // Ajouter 1 à 3 produits aléatoires à la facture
            $selectedProducts = $allProducts->random(rand(1, 3));
            $totalHT = 0;

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 5);
                $price = $product->price;
                
                $invoice->products()->attach($product->id, [
                    'quantity' => $qty,
                    'unit_price' => $price
                ]);

                $totalHT += ($price * $qty);
            }

            // Mettre à jour les totaux de la facture
            $tva = $totalHT * 0.20; // 20% TVA
            $invoice->update([
                'total_ht' => $totalHT,
                'total_tva' => $tva,
                'total_ttc' => $totalHT + $tva,
            ]);
        }
    }
}
