<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockmovement extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'change_amount',
        'movement_type',
        'description',
        'supplier',
        'ref_nota',
    ];
}
