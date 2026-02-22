@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manajemen Kategori</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                   <div class="row mb-4">

    {{-- Form Tambah Kategori --}}
    <div class="col-md-6 mb-3 mb-md-0">
        <form action="{{ route('product_categories.store') }}" method="POST" class="d-flex gap-2">
            @csrf
            <input 
                type="text" 
                class="form-control" 
                name="name" 
                placeholder="Masukkan kategori baru..."
                required
            >
            <button type="submit" class="btn btn-success">
                + Save
            </button>
            
        </form>
        <small class="text-muted">
                                    Tekan <strong>Enter</strong> untuk langsung menyimpan.
                                </small>
    </div>

    {{-- Form Search --}}
    <div class="col-md-6">
        <form action="{{ route('product_categories.index') }}" method="GET" class="d-flex gap-2">
            <input 
                type="text" 
                name="search" 
                class="form-control"
                placeholder="Search category..."
                value="{{ request()->search }}"
            >
            <button type="submit" class="btn btn-primary">
                🔍 Search
            </button>
        </form><small class="text-muted">
                                    Tekan <strong>Enter</strong> untuk mencari.
                                </small>
    </div>

</div>
                   

                    <!-- Tabel untuk menampilkan kategori produk -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>
                                        <a
                                            href="{{ route('product_categories.index', ['sort' => 'name', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            <a href="{{ route('product_categories.edit', $category) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('product_categories.destroy', $category) }}" method="POST"
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
                                {{ $categories->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection