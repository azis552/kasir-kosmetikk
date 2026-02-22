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

                    <form action="{{ route('products.updateStock', $product->id) }}" method="POST"
                        class="card shadow-sm border-0 p-4 mb-4">
                        @csrf
                        @method('PUT')

                        <h4 class="mb-4 fw-semibold">Update Stok Produk</h4>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" value="{{ $product->name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Stok Saat Ini</label>
                                <input type="text" class="form-control" value="{{ $product->stocklevel->quantity ?? 0 }}"
                                    disabled>
                            </div>
                        </div>

                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Produk</label>
                                <input type="number" autofocus class="form-control" name="change_amount" required>
                                <small class="text-muted">
                                    Tekan <strong>Enter</strong> untuk langsung menyimpan.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipe Pergerakan</label>
                                <select class="form-select" name="movement_type" required>
                                    <option value="in" selected>Masuk</option>
                                    <option value="out">Keluar</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Supplier <span class="text-muted small">(optional)</span>
                                </label>
                                <input type="text" class="form-control" name="supplier">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Ref Nota <span class="text-muted small">(optional)</span>
                                </label>
                                <input type="text" class="form-control" name="ref_nota">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Deskripsi <span class="text-muted small">(optional)</span>
                            </label>
                            <input type="text" class="form-control" name="description">
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2">
                            Simpan Perubahan
                        </button>
                    </form>
                    <div class="card">
                        <div class="card-header">
                            <h4>Stock Movements</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jumlah Perubahan</th>
                                            <th>Tipe Pergerakan</th>
                                            <th>Deskripsi</th>
                                            <th>Supplier</th>
                                            <th>Ref Nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stockMovements as $movement)
                                            <tr>
                                                <td>{{ $movement->created_at->format('d-m-Y H:i') }}</td>
                                                <td>{{ $movement->change_amount }}</td>
                                                <td>{{ $movement->movement_type == "in" ? "Masuk" : "Keluar" }}</td>
                                                <td>{{ $movement->description }}</td>
                                                <td>{{ $movement->supplier }}</td>
                                                <td>{{ $movement->ref_nota }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-center">
                                        {{ $stockMovements->appends(request()->all())->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection

@section('script')
    <!-- Tambahkan Script untuk Masking -->
    <script>
        $(document).ready(function () {
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
    </script>
@endsection