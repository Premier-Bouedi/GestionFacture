<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Crée une facture et ses produits associés au sein d'une transaction.
     */
    public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Gérer le client à la volée
            $clientId = $data['client_id'];

            if ($clientId === 'new') {
                $newClient = \App\Models\Client::create([
                    'name' => $data['new_client_name'],
                    'email' => $data['new_client_email'],
                ]);
                $clientId = $newClient->id;
            }

            // 2. Génération du numéro : FA - Année en cours - Incrément
            $year = date('Y');
            $lastInvoice = Invoice::whereYear('created_at', $year)->latest()->first();
            
            $nextNumber = $lastInvoice ? (int) substr($lastInvoice->number, -3) + 1 : 1;
            $formattedNumber = "FA-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'number' => $formattedNumber,
                'client_id' => $clientId,
                'invoice_date' => $data['invoice_date'],
            ]);

            foreach ($data['products'] as $item) {
                $product = \App\Models\Product::findOrFail($item['id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit : {$product->name}");
                }

                $product->decrement('stock', $item['quantity']);
                $invoice->products()->attach($item['id'], ['quantity' => $item['quantity']]);
            }

            return $invoice;
        });
    }

    public function updateInvoice($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $invoice = Invoice::findOrFail($id);

            // 1. Restaurer le stock ancien avant modification
            foreach ($invoice->products as $product) {
                $product->increment('stock', $product->pivot->quantity);
            }

            // 2. Détacher tous les produits actuels
            $invoice->products()->detach();

            // 3. Mettre à jour les infos de base
            $invoice->update([
                'client_id' => $data['client_id'],
                'invoice_date' => $data['invoice_date'],
            ]);

            // 4. Attacher les nouveaux produits et décrémenter le stock
            foreach ($data['products'] as $item) {
                $product = \App\Models\Product::findOrFail($item['id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit : {$product->name}");
                }

                $product->decrement('stock', $item['quantity']);
                $invoice->products()->attach($item['id'], ['quantity' => $item['quantity']]);
            }

            return $invoice;
        });
    }

    public function deleteInvoice($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::findOrFail($id);

            // Restaurer le stock avant de supprimer
            foreach ($invoice->products as $product) {
                $product->increment('stock', $product->pivot->quantity);
            }

            return $invoice->delete();
        });
    }
}
