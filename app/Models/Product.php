<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'price', 'stock'];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class)->withPivot('quantity');
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' DH';
    }
}
