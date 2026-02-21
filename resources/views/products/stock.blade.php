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
                        class="shadow p-4 rounded-lg bg-white">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-4">Tambah Produk</h4>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Produk</label>
                                <input type="text" disabled class="form-control" name="name" value="{{ $product->name }}"
                                    id="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">Stok Saat Ini</label>
                                <input type="text" class="form-control"
                                    value="{{ $product->stocklevel->quantity ?? 0 }}"
                                    placeholder="{{ $product->stocklevel->quantity ?? 0 }}"
                                    name="stock" id="stock" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="change_amount"> Quantity Product </label>
                                <input type="number" class="form-control" name="change_amount" id="change_amount">
                            </div>
                            <div class="col-md-6">
                                <label for="movement_type"> Tipe </label>
                                <select class="form-control" name="movement_type" id="movement_type" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="in">Masuk</option>
                                    <option value="out">Keluar</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supplier" class="form-label">Supplier</label> <span>(optional)</span>
                                <input type="text" class="form-control" name="supplier" id="supplier">
                            </div>
                            <div class="col-md-6">
                                <label for="ref_nota" class="form-label">Ref Nota</label><span>(optional)</span>
                                <input type="text" class="form-control" name="ref_nota" id="ref_nota">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label><span>(optional)</span>
                                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3">Simpan</button>
                        </div>
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
    </script>
@endsection
