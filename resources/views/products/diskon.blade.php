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

                    <form action="{{ route('products.storeDiskon', $product->id) }}" method="POST"
                        class="shadow p-4 rounded-lg bg-white">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-4">Tambah Produk</h4>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Produk</label>
                                <input type="text" disabled class="form-control"  name="name" value="{{ $product->name }}"
                                    id="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">Harga Produk</label>
                                <input type="text" class="form-control"
                                    value="{{ App\Helpers\FormatHelper::formatRupiah($product->price)}}"
                                    placeholder="{{ App\Helpers\FormatHelper::formatRupiah($product->price) }}"
                                    name="price" id="price" disabled>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Diskon (%)</label>
                                <input type="text" class="form-control" autofocus name="diskon_percentage" id="diskon_percentage"
                                    value="{{ $product->discount ?? old('diskon_percentage')}}" min="0" max="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="diskon amount" class="form-label">Diskon (Rp)</label>
                                <input type="text" class="form-control" name="diskon_amount" id="diskon_amount"
                                    value="{{ App\Helpers\FormatHelper::formatRupiah($product->discount_amount ?? old('diskon_amount')) }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="min_qty" class="form-label">Minimal Qty</label>
                                <input type="text" class="form-control" name="min_qty" id="min_qty"
                                    value="{{ $product->min_qty ?? old('min_qty') }}" min="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ old('start_date', $product->discount_start_date ? $product->discount_start_date->format('Y-m-d') : date('Y-m-d')) }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="{{ old('end_date', $product->discount_end_date ? $product->discount_end_date->format('Y-m-d') : \Carbon\Carbon::now()->addDays(1)->format('Y-m-d')) }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status Diskon</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="true" {{ old('status') == 'true' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="false" {{ old('status') == 'false' ? 'selected' : '' }}>
                                        Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3">Simpan</button>
                        </div>
                    </form>
                    <div class="card">
                        <div class="card-header">
                            <h4>List Diskon</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Diskon (%)</th>
                                        <th>Diskon (Rp)</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Minimal Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($diskons as $diskon)
                                        <tr>
                                            <td>{{ $diskon->diskon_percentage }}%</td>
                                            <td>{{ App\Helpers\FormatHelper::formatRupiah($diskon->diskon_amount) }}</td>
                                            <td>{{ date('d-m-Y', strtotime($diskon->start_date)) }} s/d
                                                {{ date('d-m-Y', strtotime($diskon->end_date)) }}</td>
                                            <td>{{ $diskon->is_active == '1' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                            <td>{{ $diskon->min_qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
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
            }).mask('#diskon_amount');

            Inputmask({
                alias: "numeric",
                groupSeparator: "",
                autoGroup: true,
                prefix: "",
                rightAlign: false,
                placeholder: "0"
            }).mask('#min_qty');

            Inputmask({
                alias: "numeric",
                groupSeparator: ".",
                autoGroup: true,
                suffix: " %",
                rightAlign: false,
                placeholder: "0"
            }).mask('#diskon_percentage');

            $('#diskon_percentage').change(function() {
                var percentage = parseFloat($(this).val()) || 0;
                var price = parseFloat({{ $product->price }});
                var discountAmount = (percentage / 100) * price;
                $('#diskon_amount').val(discountAmount.toFixed(0));
            });

            $('#diskon_amount').change(function() {
                var amountStr = $(this).val().replace(/[^0-9,-]+/g, "").replace(",", ".");
                var amount = parseFloat(amountStr) || 0;
                var price = parseFloat({{ $product->price }});
                var percentage = (amount / price) * 100;
                $('#diskon_percentage').val(percentage.toFixed(0));
            });
        });
    </script>
@endsection
