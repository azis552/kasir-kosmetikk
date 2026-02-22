@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manajemen Diskon Produk</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <!-- Form Pencarian -->
                    <form action="{{ route('diskon.index') }}" method="GET" class="d-flex form-inline mb-3">
                        <input autofocus type="text" name="search" class="form-control" placeholder="Search by Product Name..."
                            value="{{ request()->search }}">
                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                    </form>

                    <!-- Tabel untuk menampilkan kategori produk -->
                    <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <a
                                        href="{{ route('diskon.index', ['sort' => 'name', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                                        Produk
                                        @if (request()->sort == 'name')
                                            @if (request()->direction == 'asc')
                                                <i class="fas fa-arrow-up"></i>
                                            @else
                                                <i class="fas fa-arrow-down"></i>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Diskon (%)</th>
                                <th>Diskon (Rp)</th>
                                <th>Periode Diskon</th>
                                <th>Status</th>
                                <th>Min Qty</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($diskons as $diskon)
                                <tr>
                                    <td>{{ $diskon->product->name }}</td>
                                    <td>{{ $diskon->diskon_percentage }}</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($diskon->diskon_amount) }}</td>
                                    <td>{{ $diskon->start_date }} - {{ $diskon->end_date }}</td>
                                    <td>{{ $diskon->is_active == "1" ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    <td>{{ $diskon->min_qty }}</td>
                                    <td>
                                        <a href="{{ route('diskon.edit', $diskon) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('diskon.destroy', $diskon) }}" method="POST"
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
                                {{ $diskons->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection
