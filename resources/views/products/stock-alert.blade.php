@extends('template.master')

@section('content')
<div class="row">
    <div class="col-sm-12">

        {{-- Header Card --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                    Stock Alert
                </h5>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Produk
                </a>
            </div>
        </div>

        {{-- Summary Badges --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card bg-danger text-white">
                    <div class="card-body d-flex align-items-center py-3">
                        <i class="fas fa-times-circle fa-2x mr-3"></i>
                        <div>
                            <h6 class="mb-0">Stok Habis</h6>
                            <h3 class="mb-0 font-weight-bold">{{ $emptyProducts->count() }} Produk</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body d-flex align-items-center py-3">
                        <i class="fas fa-exclamation-circle fa-2x mr-3"></i>
                        <div>
                            <h6 class="mb-0">Hampir Habis</h6>
                            <h3 class="mb-0 font-weight-bold">{{ $lowProducts->count() }} Produk</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form action="{{ route('products.stock-alert') }}" method="GET" class="d-flex align-items-center">
                    <input type="text" name="search" class="form-control mr-2"
                        placeholder="Cari barcode atau nama produk..."
                        value="{{ $search }}" autofocus>
                    <select name="filter" class="form-control mr-2" style="width:180px;">
                        <option value="all"   {{ $filter == 'all'   ? 'selected' : '' }}>Semua</option>
                        <option value="empty" {{ $filter == 'empty' ? 'selected' : '' }}>Stok Habis</option>
                        <option value="low"   {{ $filter == 'low'   ? 'selected' : '' }}>Hampir Habis</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- ======================== --}}
        {{-- SECTION: STOK HABIS     --}}
        {{-- ======================== --}}
        @if ($filter == 'all' || $filter == 'empty')
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-times-circle mr-2"></i>
                    Stok Habis ({{ $emptyProducts->count() }} produk)
                </h6>
            </div>
            <div class="card-body p-0">
                @if ($emptyProducts->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <p class="mb-0">Tidak ada produk dengan stok habis.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kategori / Barcode</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Min Stok</th>
                                <th>Satuan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($emptyProducts as $product)
                            <tr class="table-danger">
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                </td>
                                <td>
                                    {{ $product->category->name ?? '-' }}
                                    <br>
                                    <small class="text-muted">Barcode: {{ $product->barcode }}</small>
                                </td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price_buy) }}</td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}</td>
                                <td class="text-center">
                                    <span class="badge badge-danger badge-pill">
                                        {{ $product->stocklevel->quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $product->min_stock }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    <a href="{{ route('products.stock', $product->id) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-boxes mr-1"></i>Manage Stock
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ========================== --}}
        {{-- SECTION: HAMPIR HABIS     --}}
        {{-- ========================== --}}
        @if ($filter == 'all' || $filter == 'low')
        <div class="card border-warning mt-3">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Hampir Habis ({{ $lowProducts->count() }} produk)
                </h6>
            </div>
            <div class="card-body p-0">
                @if ($lowProducts->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <p class="mb-0">Tidak ada produk yang hampir habis.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kategori / Barcode</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Min Stok</th>
                                <th>Satuan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowProducts as $product)
                            @php
                                $stok   = $product->stocklevel->quantity ?? 0;
                                $minStok = $product->min_stock;
                                // Hitung persentase sisa stok terhadap min_stock
                                $persen = $minStok > 0 ? round(($stok / $minStok) * 100) : 100;
                                $barColor = $persen <= 50 ? 'bg-danger' : 'bg-warning';
                            @endphp
                            <tr class="table-warning">
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                </td>
                                <td>
                                    {{ $product->category->name ?? '-' }}
                                    <br>
                                    <small class="text-muted">Barcode: {{ $product->barcode }}</small>
                                </td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price_buy) }}</td>
                                <td>{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}</td>
                                <td class="text-center">
                                    <span class="badge badge-warning badge-pill text-dark">
                                        {{ $stok }}
                                    </span>
                                    {{-- Progress bar sisa stok --}}
                                    <div class="progress mt-1" style="height:6px;" title="{{ $persen }}% dari min stok">
                                        <div class="progress-bar {{ $barColor }}"
                                             style="width: {{ min($persen, 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $minStok }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    <a href="{{ route('products.stock', $product->id) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-boxes mr-1"></i>Manage Stock
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
@endsection