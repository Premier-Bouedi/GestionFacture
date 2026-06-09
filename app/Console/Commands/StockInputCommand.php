<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Console\Command;

class StockInputCommand extends Command
{
    protected $signature = 'stock:input
                            {product : ID du produit}
                            {quantity : Quantité à ajouter}
                            {--description= : Motif du réapprovisionnement}';

    protected $description = 'Simule une entrée de stock (réapprovisionnement fournisseur)';

    public function handle(StockService $stockService): int
    {
        $product = Product::find($this->argument('product'));

        if (! $product) {
            $this->error('Produit introuvable.');

            return self::FAILURE;
        }

        $quantity = (int) $this->argument('quantity');

        if ($quantity <= 0) {
            $this->error('La quantité doit être supérieure à zéro.');

            return self::FAILURE;
        }

        $movement = $stockService->registerInput(
            $product,
            $quantity,
            $this->option('description')
        );

        $product->refresh();

        $this->info("Entrée enregistrée : +{$quantity} unité(s) pour « {$product->designation} ».");
        $this->line("Stock actuel : {$product->stock} | Mouvement #{$movement->id}");

        return self::SUCCESS;
    }
}
