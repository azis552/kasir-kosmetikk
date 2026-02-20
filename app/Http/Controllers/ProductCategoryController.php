<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    // Menampilkan daftar kategori produk
    public function index(Request $request)
    {
        // Ambil parameter pencarian dari request
        $search = $request->get('search');
        $sort = $request->get('sort', 'name'); // Default ke 'name' jika tidak ada
        $direction = $request->get('direction', 'asc'); // Default pengurutan adalah 'asc'

        // Query untuk mencari dan mengurutkan data
        $query = ProductCategory::query();

        // Pencarian berdasarkan nama kategori
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Pengurutan berdasarkan kolom dan arah
        $query->orderBy($sort, $direction);

        // Pagination (10 data per halaman)
        $categories = $query->paginate(10)->appends(request()->all());

        $title = 'Product Category Management';

        // Kirim data ke tampilan
        return view('product_categories.index', compact('categories', 'title'));
    }

    // Menampilkan form untuk membuat kategori produk baru
    public function create()
    {
        $title = 'Product Category Management';
        return view('product_categories.create', compact('title'));
    }

    // Menyimpan kategori produk baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
        ]);

        ProductCategory::create($request->all());  // Menyimpan data kategori produk

        return redirect()->route('product_categories.index')->with('success', 'Produk Kategori Berhasil Ditambahkan');
    }

    // Menampilkan form untuk mengedit kategori produk
    public function edit(ProductCategory $productCategory)
    {
        $title = 'Product Category Management';
        return view('product_categories.edit', compact('productCategory', 'title'));
    }

    // Memperbarui kategori produk
    public function update(Request $request, ProductCategory $productCategory)
    {
        $request->validate([
            'name' => 'required|string|max:120',
        ]);

        $productCategory->update($request->all());  // Memperbarui data kategori produk

        return redirect()->route('product_categories.index')->with('success', 'Produk Kategori Berhasil Diperbarui');
    }

    // Menghapus kategori produk
    public function destroy(ProductCategory $productCategory)
    {
        try {
            $productCategory->delete();
            return redirect()->route('product_categories.index')->with('success', 'Produk Kategori Berhasil Dihapus');
        } catch (QueryException $e) {

            // MySQL error code: 1451 = cannot delete/update parent row (FK constraint)
            if (($e->errorInfo[1] ?? null) == 1451) {
                return redirect()
                    ->back()
                    ->with('error', 'Kategori tidak bisa dihapus karena sudah digunakan pada produk. Hapus produk terkait terlebih dahulu.');
            }

            // selain itu, biarkan tetap error (atau bikin pesan general)
            throw $e;
        }
    }
}

