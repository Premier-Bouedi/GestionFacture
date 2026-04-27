<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class Product extends Model
{
    use HasFactory;

    // Technique Harik : Protection stricte des colonnes
    protected $fillable = ['designation', 'prix_unitaire', 'stock', 'code_barre', 'description'];

    // Technique Matoor : Formatage automatique pour l'API (Cast)
    protected $casts = [
        'prix_unitaire' => 'float',
        'stock' => 'integer',
    ];

    /**
     * Relation avec les factures (via la table pivot)
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_product')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }
}
