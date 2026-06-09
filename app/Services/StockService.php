<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Enregistre une entrée de stock (réapprovisionnement fournisseur).
     */
    public function registerInput(Product $product, int $quantity, ?string $description = null): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $description) {
            $product->increment('stock', $quantity);

            return StockMovement::create([
                'product_id' => $product->id,
                'type' => StockMovement::TYPE_INPUT,
                'quantity' => $quantity,
                'description' => $description ?? 'Réapprovisionnement fournisseur',
            ]);
        });
    }
}
