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
                    <a href="{{ route('diskon.index') }}" class="btn btn-primary mb-3">Kembali</a>

                    <form action="{{ route('diskon.update', $diskon->id) }}" method="POST"
                        class="shadow p-4 rounded-lg bg-white">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-4">Edit Diskon Produk</h4>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Produk</label>
                                <input type="text" disabled class="form-control" name="name" value="{{ $diskon->product->name }}"
                                    id="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">Harga Produk</label>
                                <input type="text" class="form-control"
                                    value="{{ App\Helpers\FormatHelper::formatRupiah($diskon->product->price)}}"
                                    placeholder="{{ App\Helpers\FormatHelper::formatRupiah($diskon->product->price) }}"
                                    name="price" id="price" disabled>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Diskon (%)</label>
                                <input type="text" class="form-control" name="diskon_percentage" id="diskon_percentage"
                                    value="{{ $diskon->diskon_percentage ?? old('diskon_percentage')}}" min="0" max="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="diskon amount" class="form-label">Diskon (Rp)</label>
                                <input type="text" class="form-control" name="diskon_amount" id="diskon_amount"
                                    value="{{ App\Helpers\FormatHelper::formatRupiah($diskon->diskon_amount ?? old('diskon_amount')) }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="min_qty" class="form-label">Minimal Qty</label>
                                <input type="text" class="form-control" name="min_qty" id="min_qty"
                                    value="{{ $diskon->min_qty ?? old('min_qty') }}" min="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ old('start_date', $diskon->start_date ? date('Y-m-d', strtotime($diskon->start_date)) : date('Y-m-d')) }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="{{ old('end_date', $diskon->end_date ? date('Y-m-d', strtotime($diskon->end_date)) : \Carbon\Carbon::now()->addDays(1)->format('Y-m-d')) }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status Diskon</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="true" {{ old('status',$diskon->is_Active) == 'true' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="false" {{ old('status', $diskon->is_Active) == 'false' ? 'selected' : '' }}>
                                        Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3">Simpan</button>
                        </div>
                    </form>
                 


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
                var price = parseFloat({{ $diskon->product->price }});
                var discountAmount = (percentage / 100) * price;
                $('#diskon_amount').val(discountAmount.toFixed(0));
            });

            $('#diskon_amount').change(function() {
                var amountStr = $(this).val().replace(/[^0-9,-]+/g, "").replace(",", ".");
                var amount = parseFloat(amountStr) || 0;
                var price = parseFloat({{ $diskon->product->price }});
                var percentage = (amount / price) * 100;
                $('#diskon_percentage').val(percentage.toFixed(0));
            });
        });
    </script>
@endsection
