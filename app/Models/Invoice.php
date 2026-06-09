<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'client_id',
        'invoice_date',
        'total_ht',
        'total_tva',
        'total_ttc',
        'status_email',
        'logo_path',
        'statut_livraison',
        'statut_paiement',
    ];

    /**
     * Facture réglée (éligible au bon de décharge).
     */
    public function isPaid(): bool
    {
        return $this->statut_paiement === 'Payée';
    }

    /**
     * Livraison en attente et facture payée.
     */
    public function isEligibleForDecharge(): bool
    {
        return $this->isPaid() && $this->statut_livraison === 'Non livré';
    }

    /**
     * Client facturé.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Lignes produits de la facture (table pivot).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'invoice_product')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }
}
