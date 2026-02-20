<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan pengurutan
        $search    = $request->get('search');
        $sort      = $request->get('sort', 'code');     // Default kolom untuk sort adalah 'code'
        $direction = $request->get('direction', 'asc'); // Default pengurutan adalah 'asc'

        // Query untuk mencari dan mengurutkan data
        $query = Voucher::query();

        // Pencarian berdasarkan kode voucher
        if ($search) {
            $query->where('code', 'like', "%{$search}%");
        }

        // Pengurutan berdasarkan kolom dan arah
        $query->orderBy($sort, $direction);

        // Pagination (10 data per halaman)
        $vouchers = $query->paginate(10)->appends(request()->all());
        $title    = 'Voucher Management';

        // Kirim data ke tampilan
        return view('voucher.index', compact('vouchers', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Voucher Management';
        return view('voucher.create', compact('title'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $discount_amount = str_replace(['Rp', '.', ','], '', $request->input('discount_amount'));
        $request->merge(['discount_amount' => $discount_amount]);
        $validator = Validator::make($request->all(), [
            'code'            => 'required|string|unique:vouchers,code|max:50',
            'discount_amount' => 'required|numeric|min:0',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'max_uses'        => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        Voucher::create([
            'code'            => $request->input('code'),
            'discount_amount' => $request->input('discount_amount'),
            'start_date'      => $request->input('start_date'),
            'end_date'        => $request->input('end_date'),
            'max_uses'        => $request->input('max_uses'),
            'description'     => $request->input('description'),
            'uses'            => 0,
        ]);

        return redirect()->route('vouchers.index')
                         ->with('success', 'Voucher created successfully.');
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
        $voucher = Voucher::findOrFail($id);
        $title   = 'Voucher Management';
        return view('voucher.edit', compact('voucher', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $discount_amount = str_replace(['Rp', '.', ','], '', $request->input('discount_amount'));
        $request->merge(['discount_amount' => $discount_amount]);
        $validator = Validator::make($request->all(), [
            'code'            => 'required|string|max:50|unique:vouchers,code,' . $id,
            'discount_amount' => 'required|numeric|min:0',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'max_uses'        => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        $voucher = Voucher::findOrFail($id);
        $voucher->update([
            'code'            => $request->input('code'),
            'discount_amount' => $request->input('discount_amount'),
            'start_date'      => $request->input('start_date'),
            'end_date'        => $request->input('end_date'),
            'max_uses'        => $request->input('max_uses'),
            'description'     => $request->input('description'),
            'is_active'       => $request->input('is_active'),
        ]);

        return redirect()->route('vouchers.index')
                         ->with('success', 'Voucher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('vouchers.index')
                         ->with('success', 'Voucher deleted successfully.');
    }
}
