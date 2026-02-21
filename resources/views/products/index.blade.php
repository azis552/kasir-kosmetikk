@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manajemen Produk</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Tombol untuk menambah kategori baru -->
                    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Add New Product</a>

                    <!-- Form Pencarian -->
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex form-inline mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by Product Barcode, Name..."
                            value="{{ request()->search }}" autofocus>
                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                    </form>
<div class="table-responsive">
                    <!-- Tabel untuk menampilkan kategori produk -->
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <a
                                        href="{{ route('products.index', ['sort' => 'name', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                                        Name
                                        @if (request()->sort == 'name')
                                            @if (request()->direction == 'asc')
                                                <i class="fas fa-arrow-up"></i>
                                            @else
                                                <i class="fas fa-arrow-down"></i>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Kategori</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Min Stok</th>
                                <th>Satuan</th>
                                <th>Status</th>
                                
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }} <br><span>Barcode :{{ $product->barcode }}</span> </td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price_buy) }}</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}</td>
                                    <td>{{ $product->stocklevel->quantity ?? 0 }}</td>
                                    <td>{{ $product->min_stock }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td>{{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    <td>
                                        <a href="{{ route('products.stock', $product->id) }}"
                                            class="btn btn-info btn-sm">Manage Stock</a>
                                        <a href="{{ route('products.diskon', $product) }}"
                                            class="btn btn-secondary btn-sm">Diskon</a>
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
</div>
                    <!-- Pagination Bootstrap -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                {{ $products->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection
