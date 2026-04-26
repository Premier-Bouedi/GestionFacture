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
        // 1. Créer des produits si besoin
        if (Product::count() === 0) {
            Product::create(['designation' => 'Ordinateur Portable', 'prix_unitaire' => 1200, 'stock' => 50]);
            Product::create(['designation' => 'Souris Sans Fil', 'prix_unitaire' => 25, 'stock' => 200]);
            Product::create(['designation' => 'Clavier Mécanique', 'prix_unitaire' => 80, 'stock' => 100]);
        }

        $allProducts = Product::all();
        $clients = Client::all();

        // 2. Créer des factures COHÉRENTES pour chaque client
        foreach ($clients as $client) {
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'number' => 'FACT-' . Carbon::now()->format('Ymd') . '-' . str_pad($client->id, 4, '0', STR_PAD_LEFT),
                'invoice_date' => Carbon::now()->subDays(rand(0, 30)),
                'total_ht' => 0, // Sera mis à jour après
                'total_tva' => 0,
                'total_ttc' => 0,
            ]);

            $selectedProducts = $allProducts->random(rand(1, 3));
            $totalHT = 0;

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 4);
                $price = $product->prix_unitaire;
                
                $invoice->products()->attach($product->id, [
                    'quantity' => $qty,
                    'unit_price' => $price
                ]);

                $totalHT += ($price * $qty);
            }

            // Calculs finaux (TVA à 20% par défaut)
            $tva = $totalHT * 0.20;
            $ttc = $totalHT + $tva;

            // Mise à jour de la facture avec les vrais chiffres
            $invoice->update([
                'total_ht' => $totalHT,
                'total_tva' => $tva,
                'total_ttc' => $ttc,
            ]);
        }
    }
}
