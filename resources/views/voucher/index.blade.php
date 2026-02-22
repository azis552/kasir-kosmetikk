@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manajemen Voucher</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Tombol untuk menambah kategori baru -->
                    <a href="{{ route('vouchers.create') }}" class="btn btn-primary mb-3">Add New Voucher</a>

                    <!-- Form Pencarian -->
                    <form action="{{ route('vouchers.index') }}" method="GET" class="d-flex form-inline mb-3">
                        <input type="text" autofocus name="search" class="form-control" placeholder="Search by Kode Voucher..."
                            value="{{ request()->search }}">
                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                    </form>
<div class="table-responsive">
                    <!-- Tabel untuk menampilkan kategori produk -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <a
                                        href="{{ route('vouchers.index', ['sort' => 'code', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                                        Kode Voucher
                                        @if (request()->sort == 'code')
                                            @if (request()->direction == 'asc')
                                                <i class="fas fa-arrow-up"></i>
                                            @else
                                                <i class="fas fa-arrow-down"></i>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Deskripsi</th>
                                <th>Jumlah Diskon</th>
                                <th>Periode Voucher</th>
                                <th>Status</th>
                                <th>Maks Penggunaan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vouchers as $voucher)
                                <tr>
                                    <td>{{ $voucher->code }}</td>
                                    <td>{{ $voucher->description }}</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($voucher->discount_amount) }}</td>
                                    <td>{{ $voucher->start_date }} - {{ $voucher->end_date }}</td>
                                    <td>{{ $voucher->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    <td>{{ $voucher->max_uses }}</td>
                                    
                                    <td>
                                        <a href="{{ route('vouchers.edit', $voucher) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST"
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
                                {{ $vouchers->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection
