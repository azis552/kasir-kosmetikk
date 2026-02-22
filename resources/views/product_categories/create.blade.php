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

                    <!-- Tombol untuk menambah kategori baru -->
                    <a href="{{ route('product_categories.index') }}" class="btn btn-primary mb-3">Kembali</a>

                    <form action="{{ route('product_categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" name="name" autofocus id="name" required>
                        </div>

                        <button type="submit" class="btn btn-success">Save</button>
                    </form>

                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection
