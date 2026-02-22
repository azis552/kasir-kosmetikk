@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <!-- Form Pencarian -->
                    <form action="{{ route('transaksis.riwayat') }}" method="GET" class="d-flex form-inline mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by Kode Transaksi..."
                            value="{{ request()->search }}">
                        <button type="submit" class="btn btn-primary ml-2">Search</button>
                    </form>

                    <!-- Tabel untuk menampilkan kategori produk -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>
                                        <a
                                            href="{{ route('transaksis.riwayat', ['sort' => 'transaction_code', 'direction' => request()->direction == 'asc' ? 'desc' : 'asc', 'search' => request()->search]) }}">
                                            Kode Transaksi
                                            @if (request()->sort == 'transaction_code')
                                                @if (request()->direction == 'asc')
                                                    <i class="fas fa-arrow-up"></i>
                                                @else
                                                    <i class="fas fa-arrow-down"></i>
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Petugas</th>
                                    <th>Perangkat</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_code }}</td>
                                        <td>{{ $transaction->transaction_date }}</td>
                                        <td>{{ App\Helpers\FormatHelper::formatRupiah($transaction->total) }}</td>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td>{{ $transaction->terminal_id }}</td>
                                        <td>
                                            @if($transaction->status == 'VOID')
                                            <span class="btn btn-danger">Dibatalkan </span>
                                            @else
                                            <a href="{{ route('transaksis.show', $transaction->id) }}"
                                                class="btn btn-primary">Detail</a>
                                            <button type="button" data-transaksi="{{ $transaction->id }}"
                                                class="btn btn-danger batal-paid">
                                                Batal Transaksi
                                            </button>
                                            <button id="cetak" type="button" data-transaksi="{{ $transaction->id }}"
                                                class="btn btn-warning ">
                                                Cetak
                                            </button>
                                            @endif
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
                                {{ $transactions->appends(request()->all())->links('pagination::bootstrap-5') }}
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
    <script>
        $(document).ready(function () {
            function printReceipt(receiptContent) {
                const iframe = document.createElement('iframe');
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';

                document.body.appendChild(iframe);

                const doc = iframe.contentWindow.document;
                doc.open();
                doc.write(`
                    <html>
                    <head>
                        <title>Cetak Struk</title>
                        <style>
                            @page {
                                size: 58mm auto;
                                margin: 0;
                            }
                            body {
                                width: 58mm;
                                font-family: monospace;
                                font-size: 11px;
                                white-space: pre;
                                margin: 0;
                                padding: 4px;
                            }
                        </style>
                    </head>
                    <body>
            ${receiptContent}
                    </body>
                    </html>
                `);
                doc.close();

                iframe.onload = function () {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    setTimeout(() => document.body.removeChild(iframe), 1000);
                };
            }


            $(document).on('click', '#cetak', function () {
                const transactionId = $(this).data('transaksi');
                window.open(`/kasir/cetak/${transactionId}`, '_blank');
            });

            $(document).on('click', '.batal-paid', function () {

                let transactionId = $(this).data('transaksi');

                Swal.fire({
                    title: 'Batalkan transaksi?',
                    text: 'Transaksi yang sudah dibayar akan di-VOID dan stok akan kembali.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak'
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: "{{ route('kasir.batal.paid') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                transactionId: transactionId
                            },
                            success: function (response) {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                setTimeout(() => {
                                    location.reload();
                                }, 1500);

                            },
                            error: function (xhr) {

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                                });

                            }
                        });

                    }

                });

            });


        });
    </script>
@endsection