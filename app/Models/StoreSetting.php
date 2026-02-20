<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name','phone','email','address',
        'receipt_header','receipt_footer',
        'logo_app_dark','logo_app_light','logo_doc','logo_icon','logo_receipt',
    ];
}
