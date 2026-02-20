<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan pengurutan
        $search    = $request->get('search');
        $sort      = $request->get('sort', 'name');     // Default kolom untuk sort adalah 'name'
        $direction = $request->get('direction', 'asc'); // Default pengurutan adalah 'asc'

        // Query untuk mencari dan mengurutkan data
        $query = Tax::query();

        // Pencarian berdasarkan nama pajak
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Pengurutan berdasarkan kolom dan arah
        $query->orderBy($sort, $direction);

        // Pagination (10 data per halaman)
        $taxes = $query->paginate(10)->appends(request()->all());
        $title    = 'Tax Management';

        // Kirim data ke tampilan
        return view('taxes.index', compact('taxes', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tax Management';
        return view('taxes.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $percentage = str_replace(['%'], '', $request->input('percentage'));
        $request->merge(['rate' => $percentage]);

        $request->validate([
            'name'      => 'required|string|unique:taxes,name|max:100',
            'rate'      => 'required|numeric|min:0|max:100',
            'is_active' => 'required|boolean',
        ]);

        Tax::create([
            'name'      => $request->input('name'),
            'rate'      => $request->input('rate'),
            'is_active' => $request->input('is_active'),
        ]);

        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tax = Tax::findOrFail($id);
        $title = 'Tax Management';
        return view('taxes.edit', compact('tax', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $percentage = str_replace(['%'], '', $request->input('percentage'));
        $request->merge(['rate' => $percentage]);

        $request->validate([
            'name'      => 'required|string|max:100|unique:taxes,name,' . $id,
            'rate'      => 'required|numeric|min:0|max:100',
            'is_active' => 'required|boolean',
        ]);

        $tax = Tax::findOrFail($id);
        $tax->update([
            'name'      => $request->input('name'),
            'rate'      => $request->input('rate'),
            'is_active' => $request->input('is_active'),
        ]);

        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();

        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil dihapus.');
    }
}
