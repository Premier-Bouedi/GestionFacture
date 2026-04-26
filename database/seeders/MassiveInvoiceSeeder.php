<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MassiveInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. S'assurer qu'on a des clients et produits
        if (Client::count() < 10) {
            Client::factory()->count(20)->create();
        }
        
        if (Product::count() < 5) {
            Product::create(['designation' => 'Écran 24 pouces', 'prix_unitaire' => 1500, 'stock' => 100]);
            Product::create(['designation' => 'Clavier RGB', 'prix_unitaire' => 450, 'stock' => 100]);
            Product::create(['designation' => 'Casque Audio', 'prix_unitaire' => 300, 'stock' => 100]);
        }

        $clients = Client::all();
        $products = Product::all();

        $this->command->info('Génération de 1000 factures en cours...');

        for ($i = 1; $i <= 1000; $i++) {
            $client = $clients->random();
            $date = Carbon::now()->subDays(rand(0, 365)); // Réparties sur l'année
            
            $totalHt = 0;
            $items = [];
            
            // Sélectionner 1 à 4 produits au hasard
            $selectedProducts = $products->random(rand(1, 4));
            
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'number' => 'FA-' . $date->format('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'invoice_date' => $date->format('Y-m-d'),
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 5);
                $uPrice = $product->prix_unitaire;
                $lineTotal = $uPrice * $qty;
                $totalHt += $lineTotal;

                $invoice->products()->attach($product->id, [
                    'quantity' => $qty,
                    'unit_price' => $uPrice,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            $tva = $totalHt * 0.20;
            $ttc = $totalHt + $tva;

            $invoice->update([
                'total_ht' => $totalHt,
                'total_tva' => $tva,
                'total_ttc' => $ttc,
            ]);

            if ($i % 100 === 0) {
                $this->command->info("$i factures créées...");
            }
        }

        $this->command->info('Terminé ! 1000 factures ajoutées.');
    }
}
