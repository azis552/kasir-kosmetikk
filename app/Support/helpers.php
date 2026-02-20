<?php

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('store_setting')) {
    /**
     * Ambil StoreSetting singleton (cached).
     */
    function store_setting(): StoreSetting
    {
        return Cache::rememberForever('store_setting', function () {
            return StoreSetting::query()->firstOrCreate(['id' => 1]);
        });
    }
}

if (!function_exists('store_setting_value')) {
    /**
     * Ambil value setting by key, dengan default.
     */
    function store_setting_value(string $key, $default = null)
    {
        $setting = store_setting();
        return data_get($setting, $key, $default);
    }
}

if (!function_exists('store_logo_path')) {
    /**
     * Ambil path logo (relative storage) untuk tipe tertentu.
     * type: app_dark | app_light | doc | icon | receipt
     */
    function store_logo_path(string $type): ?string
    {
        $map = [
            'app_dark' => 'logo_app_dark',
            'app_light' => 'logo_app_light',
            'doc' => 'logo_doc',
            'icon' => 'logo_icon',
            'receipt' => 'logo_receipt',
        ];

        $field = $map[$type] ?? null;
        if (!$field)
            return null;

        return store_setting_value($field);
    }
}

if (!function_exists('store_logo')) {
    /**
     * Ambil URL logo (siap dipakai di <img src="...">)
     * fallback: kalau null, return null (atau bisa kamu set default asset)
     */
    function store_logo(string $type): ?string
    {
        $path = store_logo_path($type);
        return $path ? asset('storage/' . $path) : null;
    }
}

if (!function_exists('store_setting_clear_cache')) {
    /**
     * Hapus cache setting (panggil setelah update).
     */
    function store_setting_clear_cache(): void
    {
        Cache::forget('store_setting');
    }
}

if (! function_exists('store_name')) {
    function store_name(string $default = 'Toko'): string
    {
        return (string) store_setting_value('store_name', $default);
    }
}

if (! function_exists('store_phone')) {
    function store_phone(?string $default = null): ?string
    {
        $v = store_setting_value('phone', $default);
        return $v ? (string) $v : null;
    }
}

if (! function_exists('store_email')) {
    function store_email(?string $default = null): ?string
    {
        $v = store_setting_value('email', $default);
        return $v ? (string) $v : null;
    }
}

if (! function_exists('store_address')) {
    function store_address(?string $default = null): ?string
    {
        $v = store_setting_value('address', $default);
        return $v ? (string) $v : null;
    }
}

if (! function_exists('store_receipt_header')) {
    function store_receipt_header(?string $default = null): ?string
    {
        $v = store_setting_value('receipt_header', $default);
        return $v ? (string) $v : null;
    }
}

if (! function_exists('store_receipt_footer')) {
    function store_receipt_footer(?string $default = null): ?string
    {
        $v = store_setting_value('receipt_footer', $default);
        return $v ? (string) $v : null;
    }
}

if (! function_exists('store_default_tax_id')) {
    function store_default_tax_id(): ?int
    {
        $id = store_setting_value('default_tax_id');
        return $id ? (int) $id : null;
    }
}

