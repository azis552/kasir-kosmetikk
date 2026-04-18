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
                        <h4 class="mb-4">Edit Produk</h4>

                        <div class="row mb-3">
                             <div class="col-md-6">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control" name="barcode" autofocus value="{{ $product->barcode }}" id="barcode">
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
        <label for="price_buy" class="form-label">Harga Beli</label>
        <input
            type="number"
            class="form-control"
            name="price_buy"
            id="price_buy"
            value="{{ $product->price_buy }}"
            min="0"
            required
            oninput="
                const sell = document.getElementById('price_sell');
                if (sell.value !== '' && Number(this.value) >= Number(sell.value)) {
                    this.setCustomValidity('HPP harus lebih rendah dari harga jual');
                } else {
                    this.setCustomValidity('');
                    sell.setCustomValidity('');
                }
            "
        >
    </div>

    <div class="col-md-4">
        <label for="price_sell" class="form-label">Harga Jual</label>
        <input
            type="number"
            class="form-control"
            name="price_sell"
            id="price_sell"
            value="{{ $product->price }}"
            min="1"
            required
            oninput="
                const buy = document.getElementById('price_buy');
                if (buy.value !== '' && Number(this.value) <= Number(buy.value)) {
                    this.setCustomValidity('Harga jual harus lebih tinggi dari HPP');
                } else {
                    this.setCustomValidity('');
                    buy.setCustomValidity('');
                }
            "
        >
    </div>
                            <div class="col-md-4">
                                <label for="min_stock" class="form-label">Min Stok</label>
                                <input type="number" class="form-control" name="min_stock"
                                    value="{{ $product->min_stock }}" id="min_stock" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Satuan</label>
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
                            <small class="text-muted">
                                    Tekan <strong>Enter</strong> untuk langsung menyimpan.
                                </small>
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
    <!-- <script>
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
    </script> -->
@endsection
