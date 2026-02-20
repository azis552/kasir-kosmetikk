@extends('template.master')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Detail Transaksi</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div>
                        <h6>Kode Transaksi: {{ $transaction->transaction_code }}</h6>
                        <h6>Nama Pelanggan: {{ $transaction->pelanggan_name ?? '-' }}</h6>
                        <h6>Kasir: {{ $transaction->user->name ?? '-' }}</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th colspan="2" style="text-align: center">Diskon</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->transaction_details as $detail)
                                    <tr>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>{{ App\Helpers\FormatHelper::formatRupiah($detail->product->price) }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ $detail->diskonNota->diskon_percentage ?? 0 }}%</td>
                                        <td>{{ App\Helpers\FormatHelper::formatRupiah($detail->diskonNota->diskon_amount ?? 0) }}
                                        </td>
                                        <td>{{ App\Helpers\FormatHelper::formatRupiah($detail->line_total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">Grand Total</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($transaction->subtotal) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">Diskon</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($transaction->diskon_item) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Voucher</td>
                                    <td>{{ $transaction->voucher->code ?? '-' }}</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($transaction->potongan_voucher) }}</td>

                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Pajak</td>
                                    <td class="pajak-footer" style="text-align: right;">{{ $transaction->tax }}%</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($transaction->tax_amount) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">Total</td>
                                    <td>{{ App\Helpers\FormatHelper::formatRupiah($transaction->total) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <style>
                            .pajak-footer {
                                width: 50px;
                                /* Sesuaikan dengan lebar yang diinginkan */
                            }
                        </style>

                    </div>
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection