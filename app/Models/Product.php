<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'description',
        'prix_unitaire',
        'stock',
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
