@extends('template.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Stock Alert</h5>
            </div>
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Summary --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-times-circle mr-1"></i>
                            Stok Habis: <strong>{{ $emptyProducts->count() }} produk</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Hampir Habis: <strong>{{ $lowProducts->count() }} produk</strong>
                        </div>
                    </div>
                </div>

                {{-- Search & Filter --}}
                <form action="{{ route('products.stock-alert') }}" method="GET" class="d-flex form-inline mb-3">
                    <input autofocus type="text" name="search" class="form-control"
                        placeholder="Search by Product Barcode, Name..."
                        value="{{ $search }}">
                    <select name="filter" class="form-control ml-2" style="width:160px;">
                        <option value="all"   {{ $filter == 'all'   ? 'selected' : '' }}>Semua</option>
                        <option value="empty" {{ $filter == 'empty' ? 'selected' : '' }}>Stok Habis</option>
                        <option value="low"   {{ $filter == 'low'   ? 'selected' : '' }}>Hampir Habis</option>
                    </select>
                    <button type="submit" class="btn btn-primary ml-2">Search</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary ml-2">Kembali ke Produk</a>
                </form>

                {{-- TABEL STOK HABIS --}}
                @if ($filter == 'all' || $filter == 'empty')
                <h6 class="text-danger mt-2 mb-2">
                    <i class="fas fa-times-circle mr-1"></i>
                    Stok Habis ({{ $emptyProducts->count() }} produk)
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
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
                            @forelse ($emptyProducts as $product)
                            <tr class="table-danger">
                                <td>{{ $product->name }}</td>
                                <td>
                                    {{ $product->category->name ?? '-' }}
                                    <br><span>Barcode: {{ $product->barcode }}</span>
                                </td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price_buy) }}</td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}</td>
                                <td>{{ $product->stocklevel->quantity ?? 0 }}</td>
                                <td>{{ $product->min_stock }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                                <td>
                                    <a href="{{ route('products.stock', $product->id) }}" class="btn btn-info btn-sm">Manage Stock</a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">
                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                    Tidak ada produk dengan stok habis.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- TABEL HAMPIR HABIS --}}
                @if ($filter == 'all' || $filter == 'low')
                <h6 class="text-warning mt-2 mb-2">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Hampir Habis ({{ $lowProducts->count() }} produk)
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
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
                            @forelse ($lowProducts as $product)
                            @php
                                $stok    = $product->stocklevel->quantity ?? 0;
                                $minStok = $product->min_stock;
                                $persen  = $minStok > 0 ? round(($stok / $minStok) * 100) : 100;
                            @endphp
                            <tr class="table-warning">
                                <td>{{ $product->name }}</td>
                                <td>
                                    {{ $product->category->name ?? '-' }}
                                    <br><span>Barcode: {{ $product->barcode }}</span>
                                </td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price_buy) }}</td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}</td>
                                <td>
                                    {{ $stok }}
                                    <div class="progress mt-1" style="height:4px;" title="{{ $persen }}% dari min stok">
                                        <div class="progress-bar {{ $persen <= 50 ? 'bg-danger' : 'bg-warning' }}"
                                             style="width:{{ min($persen,100) }}%"></div>
                                    </div>
                                </td>
                                <td>{{ $minStok }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                                <td>
                                    <a href="{{ route('products.stock', $product->id) }}" class="btn btn-info btn-sm">Manage Stock</a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">
                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                    Tidak ada produk yang hampir habis.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection