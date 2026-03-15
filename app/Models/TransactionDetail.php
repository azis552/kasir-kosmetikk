<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    // FIX: ganti guarded=[] dengan fillable eksplisit
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
        'price_buy',
        'diskon_id',
        'discount',
        'line_total',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function stock_product()
    {
        return $this->hasOne(Stocklevel::class, 'product_id', 'product_id');
    }

    public function diskons()
    {
        return $this->hasMany(DiskonProduk::class, 'product_id', 'product_id');
    }

    public function diskonNota()
    {
        return $this->hasOne(DiskonProduk::class, 'product_id', 'product_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }

    public function diskonLaporan()
    {
        return $this->hasOne(DiskonProduk::class, 'id', 'diskon_id');
    }
}