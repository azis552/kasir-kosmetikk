<?php
namespace App\Helpers;

use App\Models\Tax;
use NumberFormatter;

class FormatHelper
{
    public static function formatRupiah($amount)
    {
        $locale = 'id_ID';
        $fmt    = new NumberFormatter($locale, NumberFormatter::CURRENCY);

// Setel jumlah desimal ke 0 untuk menghilangkan bagian desimal
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

        return $fmt->formatCurrency($amount, 'IDR');

    }

    public static function getMacAddress()
    {
        // Jalankan perintah getmac untuk Windows
        $macAddress = shell_exec('getmac');

        // Gunakan regular expression untuk mencari MAC Address
        preg_match_all('/([A-F0-9]{2}[-]){5}[A-F0-9]{2}/', $macAddress, $matches);

        // Jika ada MAC Address yang ditemukan, ambil MAC pertama
        $mac = $matches[0][0] ?? 'MAC Address tidak ditemukan';

        return $mac;
    }

    public static function tax(){
        $tax = Tax::where('is_active', 1)->orderBy('created_at', 'desc')->first();
        $tax = $tax ? $tax->rate : 0;
        return $tax;
    }

    public static function taxName(){
        $tax = Tax::where('is_active', 1)->orderBy('created_at', 'desc')->first();
        $tax = $tax ? $tax->name : '';
        return $tax;
    }

    public static function taxCount(){
        $tax = Tax::where('is_active', 1)->count();
        return $tax;
    }


}
