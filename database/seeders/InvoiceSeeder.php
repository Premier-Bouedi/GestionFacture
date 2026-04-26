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
        // 1. Créer des produits avec le nouveau schéma
        if (Product::count() === 0) {
            Product::create(['designation' => 'Ordinateur Portable', 'prix_unitaire' => 1200, 'stock' => 50]);
            Product::create(['designation' => 'Souris Sans Fil', 'prix_unitaire' => 25, 'stock' => 200]);
            Product::create(['designation' => 'Clavier Mécanique', 'prix_unitaire' => 80, 'stock' => 100]);
        }

        $allProducts = Product::all();
        $clients = Client::all();

        // 2. Créer des factures pour les clients
        foreach ($clients as $client) {
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'invoice_date' => Carbon::now()->subDays(rand(0, 30)),
            ]);

            $selectedProducts = $allProducts->random(rand(1, 2));
            foreach ($selectedProducts as $product) {
                $invoice->products()->attach($product->id, [
                    'quantity' => rand(1, 3),
                    'unit_price' => $product->prix_unitaire // On enregistre le prix pivot
                ]);
            }
        }
    }
}
