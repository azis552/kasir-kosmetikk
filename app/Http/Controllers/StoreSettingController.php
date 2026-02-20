<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreSettingController extends Controller
{
    public function edit()
    {
        $title = 'Setting Toko';
        $setting = StoreSetting::firstOrCreate(['id' => 1]);

        return view('setting.toko', compact('setting', 'title'));
    }

    public function update(Request $request)
    {
        $setting = StoreSetting::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'store_name' => ['required','string','max:100'],
            'phone'      => ['nullable','string','max:30'],
            'email'      => ['nullable','email','max:100'],
            'address'    => ['nullable','string','max:255'],

            'receipt_header' => ['nullable','string','max:100'],
            'receipt_footer' => ['nullable','string','max:500'],

            // upload gambar (aman: png/jpg/webp, max 2MB)
            'logo_app_dark'  => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
            'logo_app_light' => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
            'logo_doc'       => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
            'logo_icon'      => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
            'logo_receipt'   => ['nullable','image','mimes:png,jpg,jpeg,webp','max:2048'],
        ]);

        DB::transaction(function () use ($request, $setting, $validated) {
            // update text fields
            $setting->fill([
                'store_name' => $validated['store_name'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'receipt_header' => $validated['receipt_header'] ?? null,
                'receipt_footer' => $validated['receipt_footer'] ?? null,
            ]);

            // helper upload
            $upload = function (string $field) use ($request, $setting) {
                if (!$request->hasFile($field)) return;

                // hapus file lama (opsional)
                $old = $setting->{$field};
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }

                // simpan file baru
                $path = $request->file($field)->store('settings', 'public');
                $setting->{$field} = $path;
            };

            // upload file logo
            $upload('logo_app_dark');
            $upload('logo_app_light');
            $upload('logo_doc');
            $upload('logo_icon');
            $upload('logo_receipt');

            $setting->save();
            store_setting_clear_cache();

        });

        return back()->with('success', 'Setting toko berhasil disimpan.');
    }
}
