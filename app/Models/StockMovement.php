<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const TYPE_INPUT = 'input';
    public const TYPE_OUTPUT = 'output';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'description',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Produit concerné par ce mouvement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
