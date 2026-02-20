<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
   public function index()
    {
        $theme = AppSetting::first();
        $title = 'Pengaturan Tema';
        return view('admin.theme', compact('theme', 'title'));
    }

    public function update(Request $request)
    {
        $theme = AppSetting::first();

        $theme->update($request->only([
            'primary_color',
            'secondary_color',
            'sidebar_color',
            'background_color',
            'text_color'
        ]));

        return back()->with('success', 'Tema berhasil diperbarui');
    }
}
