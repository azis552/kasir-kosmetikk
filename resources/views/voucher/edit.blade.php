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
                    <a href="{{ route('vouchers.index') }}" class="btn btn-primary mb-3">Kembali</a>

                    <form action="{{ route('vouchers.update', $voucher->id) }}" method="POST" class="shadow p-4 rounded-lg bg-white">
                        @csrf
                        @method('PUT')

                        <h4 class="mb-4">Ubah Voucher</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Kode Voucher</label>
                                <input type="text" class="form-control" name="code" id="code" autofocus value="{{ old('code', $voucher->code) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="is_active" id="is_active" required>
                                    <option value="1" {{ old('is_active', $voucher->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $voucher->is_active) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="discount_amount" class="form-label">Jumlah Voucher (Rp)</label>
                                <input type="text" class="form-control" name="discount_amount" id="discount_amount" value=" {{ old('discount_amount', $voucher->discount_amount ?? 0) }}" placeholder="Rp" required>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" value="{{ old('start_date', $voucher->start_date ? date('Y-m-d', strtotime($voucher->start_date)) : date('Y-m-d')) }}" name="start_date" id="start_date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $voucher->end_date ? date('Y-m-d', strtotime($voucher->end_date)) : \Carbon\Carbon::now()->addDays(1)->format('Y-m-d')) }}" id="end_date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="max_uses" class="form-label">Maksimal Penggunaan</label>
                                <input type="text" class="form-control" name="max_uses" id="max_uses" value="{{ old('max_uses', $voucher->max_uses ?? 0) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $voucher->description) }}</textarea>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
            }).mask('#discount_amount');

            Inputmask({
                alias: "numeric",
                groupSeparator: "",
                autoGroup: true,
                prefix: "",
                rightAlign: false,
                placeholder: "0"
            }).mask('#max_uses');
        });
    </script>
@endsection
