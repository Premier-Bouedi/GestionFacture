<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // Technique Harik : Protection stricte des colonnes
    protected $fillable = [
        'category_id',
        'designation',
        'image',
        'prix_unitaire',
        'stock',
        'code_barre',
        'description',
    ];

    protected $appends = ['image_url'];

    // Technique Matoor : Formatage automatique pour l'API (Cast)
    protected $casts = [
        'prix_unitaire' => 'float',
        'stock' => 'integer',
    ];

    /**
     * Catégorie du produit.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Historique des mouvements de stock.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Factures contenant ce produit (table pivot).
     */
    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_product')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-product.png');
    }
}
