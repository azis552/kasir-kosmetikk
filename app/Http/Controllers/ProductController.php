<?php
namespace App\Http\Controllers;

use App\Models\DiskonProduk;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\Stocklevel;
use App\Models\Stockmovement;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan pengurutan
        $search = $request->get('search');
        $sort = $request->get('sort', 'name');     // Default kolom untuk sort adalah 'name'
        $direction = $request->get('direction', 'asc'); // Default pengurutan adalah 'asc'

        // Query untuk mencari dan mengurutkan data
        $query = Products::query();

        // Pencarian berdasarkan nama role
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Pengurutan berdasarkan kolom dan arah
        $query->orderBy($sort, $direction);

        // Pagination (10 data per halaman)
        $products = $query->paginate(10)->appends(request()->all());
        $title = 'Product Management';

        // Kirim data ke tampilan
        return view('products.index', compact('products', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Product Management';
        $categories = ProductCategory::all();
        return view('products.create', compact('title', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $price = str_replace(['Rp', '.', ',', ' '], ['', '', '', ''], $request->input('price_sell'));
        $price_buy = str_replace(['Rp', '.', ',', ' '], ['', '', '', ''], $request->input('price_buy'));
        $request->merge(['price' => $price, 'price_buy' => $price_buy]);
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string|max:50|unique:products,barcode',
            'sku' => 'string|max:50|unique:products,sku',
            'name' => 'required|string|max:120',
            'category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'price_buy' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'min_stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Products::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully');
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

        $product = Products::findOrFail($id);
        $categories = ProductCategory::all();
        $title = 'Product Management';
        return view('products.edit', compact('product', 'categories', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $price = str_replace(['Rp', '.', ',', ' '], ['', '', '', ''], $request->input('price_sell'));
        $price_buy = str_replace(['Rp', '.', ',', ' '], ['', '', '', ''], $request->input('price_buy'));
        $request->merge(['price' => $price, 'price_buy' => $price_buy]);
        $product = Products::findOrFail($id);
        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Products::findOrFail($id);
            $product->delete();

            return redirect()
                ->back()
                ->with('success', 'Produk berhasil dihapus.');
        } catch (QueryException $e) {

            // MySQL error code: 1451 = cannot delete/update parent row (FK constraint)
            if (($e->errorInfo[1] ?? null) == 1451) {
                return redirect()
                    ->back()
                    ->with('error', 'Produk tidak bisa dihapus karena sudah digunakan pada transaksi. Hapus/ubah transaksi terkait terlebih dahulu.');
            }

            // selain itu, biarkan tetap error (atau bikin pesan general)
            throw $e;
        }
    }

    public function stock($productId)
    {
        $product = Products::findOrFail($productId);
        $stockMovements = Stockmovement::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $title = 'Product Stock Management';
        return view('products.stock', compact('product', 'stockMovements', 'title'));
    }

    public function updateStock(Request $request, $productId)
    {
        $product = Products::findOrFail($productId);

        $movement_type = $request->input('movement_type');
        $request->validate([
            'change_amount' => 'required|integer',
            'movement_type' => 'required|string|in:in,out',
            'description' => 'nullable|string',
            'supplier' => 'nullable|string',
            'ref_nota' => 'nullable|string',
        ]);

        $StockMove = Stockmovement::create([
            'product_id' => $productId,
            'change_amount' => $request->input('change_amount'),
            'movement_type' => $request->input('movement_type'),
            'description' => $request->input('description'),
            'supplier' => $request->input('supplier'),
            'ref_nota' => $request->input('ref_nota'),
        ]);
        // Update stok produk
        $stock = Stocklevel::where('product_id', $productId)->first();
        if (!$stock) {
            // Jika belum ada record stok, buat baru dengan quantity 0
            $stock = Stocklevel::create([
                'product_id' => $productId,
                'quantity' => 0,
            ]);
        }
        $stock_now = $stock->quantity;

        if ($movement_type == "in") {
            $quantity_baru = $stock_now + $request->input('change_amount');
        } else {
            $quantity_baru = $stock_now - $request->input('change_amount');
        }

        $stock->quantity = $quantity_baru;
        $stock->save();

        return redirect()->route('products.index')->with('success', 'Stock updated successfully');
    }

    public function diskon($productId)
    {
        $product = Products::findOrFail($productId);
        $title = 'Product Discount Management';
        $diskons = DiskonProduk::where('product_id', $productId)
            ->paginate(10);
        return view('products.diskon', compact('product', 'title', 'diskons'));
    }

    public function storeDiskon(Request $request, $productId)
    {
        $diskon_percentage = str_replace(['Rp', '.', ',', ' %', ' '], ['', '', '', '', ''], $request->input('diskon_percentage'));

        $diskon_amount = str_replace(['Rp', '.', ',', ' '], ['', '', '', ''], $request->input('diskon_amount'));
        $status = $request->input('status') === 'true' ? 1 : 0;
        $request->merge([
            'diskon_percentage' => $diskon_percentage,
            'diskon_amount' => $diskon_amount,
            'status' => $status,
        ]);
        $validator = Validator::make($request->all(), [
            'diskon_percentage' => 'nullable|integer|min:0|max:100',
            'diskon_amount' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'min_qty' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DiskonProduk::create([
            'product_id' => $productId,
            'diskon_percentage' => $request->input('diskon_percentage'),
            'diskon_amount' => $request->input('diskon_amount'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'is_active' => $request->input('status'),
            'min_qty' => $request->input('min_qty'),
        ]);

        return redirect()->route('products.diskon', $productId)->with('success', 'Discount created successfully');
    }
}
