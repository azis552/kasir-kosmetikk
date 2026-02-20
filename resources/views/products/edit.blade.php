@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">

        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tombol untuk menambah kategori baru -->
                    <a href="{{ route('products.index') }}" class="btn btn-primary mb-3">Kembali</a>

                    <form action="{{ route('products.update', $product->id) }}" method="POST"
                        class="shadow p-4 rounded-lg bg-white">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-4">Tambah Produk</h4>

                        <div class="row mb-3">
                             <div class="col-md-6">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control" name="barcode" value="{{ $product->barcode }}" id="barcode">
                                <span>Contoh: 123456789 </span>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="name" value="{{ $product->name }}"
                                    id="name" required>
                            </div>
                            
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Harga Beli</label>
                                <input type="text" class="form-control"
                                    value="{{ App\Helpers\FormatHelper::formatRupiah($product->price_buy) }}"
                                    placeholder="{{ App\Helpers\FormatHelper::formatRupiah($product->price_buy) }}"
                                    name="price_buy" id="price_buy" required>
                            </div>
                            <div class="col-md-4">
                                <label for="price" class="form-label">Harga</label>
                                <input type="text" class="form-control"
                                    value="{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}"
                                    placeholder="{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}"
                                    name="price" id="price" required>
                            </div>
                            <div class="col-md-4">
                                <label for="min_stock" class="form-label">Min Stok</label>
                                <input type="number" class="form-control" name="min_stock"
                                    value="{{ $product->min_stock }}" id="min_stock" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Unit</label>
                                <input type="text" class="form-control" name="unit" value="{{ $product->unit }}"
                                    id="unit" required>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="is_active" id="is_active" required>
                                    <option value="1" {{ $product->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $product->is_active == 0 ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            
                            <div class="col-md-6">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control" value="{{ $product->sku }}" name="sku"
                                    id="sku">
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori Produk</label>
                                <select class="form-control" name="category_id" id="category_id" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3">Simpan</button>
                        </div>
                    </form>





                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection

@section('script')
    <!-- Tambahkan Script untuk Masking -->
    <script>
        $(document).ready(function() {
            // Apply Inputmask for the price field with prefix 'Rp' and thousands separator
            Inputmask({
                alias: "numeric",
                groupSeparator: ".",
                autoGroup: true,
                prefix: "Rp ",
                rightAlign: false,
                placeholder: "0"
            }).mask('#price');
        });

        $(document).ready(function() {
            // Apply Inputmask for the price field with prefix 'Rp' and thousands separator
            Inputmask({
                alias: "numeric",
                groupSeparator: ".",
                autoGroup: true,
                prefix: "Rp ",
                rightAlign: false,
                placeholder: "0"
            }).mask('#price_buy');
        })
    </script>
@endsection
