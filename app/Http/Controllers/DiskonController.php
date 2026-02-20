<?php

namespace App\Http\Controllers;

use App\Models\DiskonProduk;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiskonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $diskons = DiskonProduk::with('product')
                ->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->appends(request()->all());
        }else {
            $diskons = DiskonProduk::with('product')
                ->paginate(10)
                ->appends(request()->all());
        }
        $title  = 'Diskon Produk Management';
        return view('diskon.index', compact('diskons', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $diskon = DiskonProduk::findOrFail($id);
        $title  = 'Edit Diskon Produk';
        return view('diskon.edit', compact('diskon', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $diskon_percentage = str_replace(['Rp', '.', ',', ' %', ' '], ['', '', '', '', ''], $request->input('diskon_percentage'));
        $diskon_amount     = str_replace(['Rp', '.', ',', ' '], ['', '', '', ''], $request->input('diskon_amount'));
        $status = $request->input('status') === 'true' ? 1 : 0;
        $request->merge([
            'diskon_percentage' => $diskon_percentage,
            'diskon_amount'     => $diskon_amount,
            'is_active'            => $status,
        ]);
        $validator = Validator::make($request->all(), [
            'diskon_percentage' => 'nullable|integer|min:0|max:100',
            'diskon_amount'     => 'nullable|integer|min:0',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'is_active'            => 'required|boolean',
            'min_qty'           => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DiskonProduk::findOrFail($id)->update($request->all());
        return redirect()->route('diskon.index')->with('success', 'Diskon Produk updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DiskonProduk::findOrFail($id)->delete();
            return redirect()->route('diskon.index')->with('success', 'Diskon Produk deleted successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Diskon Produk tidak bisa dihapus karena sudah digunakan pada transaksi. Hapus/ubah transaksi terkait terlebih dahulu.');
        }

        DiskonProduk::findOrFail($id)->delete();
        return redirect()->route('diskon.index')->with('success', 'Diskon Produk deleted successfully');
    }
}
