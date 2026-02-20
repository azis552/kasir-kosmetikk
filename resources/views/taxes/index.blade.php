@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manajemen Pajak</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Tombol untuk menambah kategori baru -->
                    <a href="{{ route('taxes.create') }}" class="btn btn-primary mb-3">Add New Tax</a>

                    <!-- Form Pencarian -->
                    <form action="{{ route('taxes.index') }}" method="GET" class="d-flex form-inline mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by Nama Pajak..."
                            value="{{ request()->search }}">
                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                    </form>

                    <!-- Tabel untuk menampilkan kategori produk -->
                    <div class="table-responsive">
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <th>
                                    <a
                                        href="{{ route('taxes.index', ['sort' => 'name', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                                        Nama Pajak
                                        @if (request()->sort == 'name')
                                            @if (request()->direction == 'asc')
                                                <i class="fas fa-arrow-up"></i>
                                            @else
                                                <i class="fas fa-arrow-down"></i>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Persentase</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($taxes as $tax)
                                <tr>
                                    <td>{{ $tax->name }}</td>
                                    <td>{{ $tax->rate }} %</td>
                                    <td>{{ $tax->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    
                                    
                                    <td>
                                        <a href="{{ route('taxes.edit', $tax) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('taxes.destroy', $tax) }}" method="POST"
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
                                {{ $taxes->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection
