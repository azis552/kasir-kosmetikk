@extends('template.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Detail Transaksi</h5>
                
            </div>

            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- ===== INFORMASI TRANSAKSI ===== --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Kode Transaksi:</strong><br>
                        {{ $transaction->transaction_code }}
                    </div>
                    <div class="col-md-4">
                        <strong>Pelanggan:</strong><br>
                        {{ $transaction->pelanggan_name ?? '-' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Kasir:</strong><br>
                        {{ $transaction->user->name ?? '-' }}
                    </div>
                    <div class="col-md-4 mt-2">
                        <strong>Tanggal:</strong><br>
                        {{ $transaction->created_at->format('d-m-Y H:i') }}
                    </div>
                    <div class="col-md-4 mt-2">
                        <strong>Metode Pembayaran:</strong><br>
                        {{ strtoupper($transaction->payment_method) }}
                    </div>
                </div>

                {{-- ===== TABEL ITEM ===== --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Diskon (Rp)</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->transaction_details as $detail)
                                <tr>
                                    <td class="text-start">
                                        {{ $detail->product->name }}
                                    </td>

                                    <td>
                                        {{ App\Helpers\FormatHelper::formatRupiah($detail->price) }}
                                    </td>

                                    <td>
                                        {{ $detail->quantity }}
                                    </td>

                                    <td>
                                        {{ App\Helpers\FormatHelper::formatRupiah($detail->discount ?? 0) }}
                                    </td>

                                    <td>
                                        {{ App\Helpers\FormatHelper::formatRupiah($detail->line_total) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        {{-- ===== FOOTER TOTAL ===== --}}
                        <tfoot class="fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">
                                    Subtotal (Sebelum Diskon)
                                </td>
                                <td>
                                    {{ App\Helpers\FormatHelper::formatRupiah(
                                        $transaction->subtotal + $transaction->diskon_item
                                    ) }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" class="text-end">
                                    Total Diskon Item
                                </td>
                                <td>
                                    {{ App\Helpers\FormatHelper::formatRupiah($transaction->diskon_item) }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" class="text-end">
                                    Voucher
                                </td>
                                <td>
                                    {{ App\Helpers\FormatHelper::formatRupiah($transaction->potongan_voucher) }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" class="text-end">
                                    Pajak ({{ $transaction->tax }}%)
                                </td>
                                <td>
                                    {{ App\Helpers\FormatHelper::formatRupiah($transaction->tax_amount) }}
                                </td>
                            </tr>

                            <tr class="table-success">
                                <td colspan="4" class="text-end">
                                    Total Akhir
                                </td>
                                <td>
                                    {{ App\Helpers\FormatHelper::formatRupiah($transaction->total) }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" class="text-end">
                                    Dibayar
                                </td>
                                <td>
                                    {{ App\Helpers\FormatHelper::formatRupiah($transaction->dibayar) }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" class="text-end">
                                    Kembalian
                                </td>
                                <td>
                                    {{ App\Helpers\FormatHelper::formatRupiah($transaction->kembalian) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection