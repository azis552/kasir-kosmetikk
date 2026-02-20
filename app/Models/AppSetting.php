<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'primary_color',
        'secondary_color',
        'sidebar_color',
        'background_color',
        'text_color',
    ];

    public static function getSettings()
    {
        return self::first();
    }
}

