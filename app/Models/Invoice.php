<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use App\Models\Product;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number', 
        'client_id', 
        'invoice_date', 
        'total_ht', 
        'total_tva', 
        'total_ttc'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }
}
