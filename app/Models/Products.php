<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'sku',
        'name',
        'category_id',
        'price',
        'unit',
        'min_stock',
        'is_active',
        'price_buy',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function stocklevel()
    {
        return $this->hasOne(Stocklevel::class, 'product_id');
    }

    public function diskonProduks()
    {
        return $this->hasMany(DiskonProduk::class, 'product_id');
    }
}
