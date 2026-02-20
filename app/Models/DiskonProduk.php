<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiskonProduk extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'diskon_percentage',
        'diskon_amount',
        'start_date',
        'end_date',
        'is_active',
        'min_qty',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }


    public function scopeActive(Builder $q, $at = null): Builder
    {
        $at = $at ? \Carbon\Carbon::parse($at) : now();

        return $q->where('is_active', 1)
            ->where(function ($q2) use ($at) {
                // start_date boleh null berarti langsung aktif
                $q2->whereNull('start_date')
                    ->orWhere('start_date', '<=', $at);
            })
            ->where(function ($q2) use ($at) {
                // end_date null berarti tidak ada batas
                $q2->whereNull('end_date')
                    ->orWhere('end_date', '>=', $at);
            });
    }
}
