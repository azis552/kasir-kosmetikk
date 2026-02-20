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
                    <a href="{{ route('taxes.index') }}" class="btn btn-primary mb-3">Kembali</a>

                    <form action="{{ route('taxes.update', $tax->id) }}" method="POST" class="shadow p-4 rounded-lg bg-white">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-4">Edit Pajak</h4>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label">Nama Pajak</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $tax->name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="percentage" class="form-label">Persentase (%)</label>
                                <input type="text" class="form-control" name="percentage" id="percentage" value="{{ old('percentage', $tax->rate) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="is_active" id="is_active" required>
                                    <option value="1" {{ old('is_active', $tax->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $tax->is_active) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
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
                suffix: " %",
                rightAlign: false,
                placeholder: "0"
            }).mask('#percentage');

        });
    </script>
@endsection
