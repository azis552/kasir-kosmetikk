<?php
namespace App\Helpers;

use App\Models\AppSetting;

class ThemeHelper
{
    public static function get()
    {
        return AppSetting::first();
    }
}
