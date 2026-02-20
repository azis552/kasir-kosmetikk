@extends('template.master')

@section('content')
    <div class="container">
        <div class="card p-2">
            <div class="card-header">
                <h5>Laporan Bulanan {{ $bulan }}</h5>
                <form class="d-flex align-items-center mt-3" action="{{ route('laporan.laporan_bulanan') }}" method="GET">

                    <input type="month" name="bulan" id="bulan" class="form-control mr-2"
                        value="{{ request('bulan') ?? $bulan ?? now()->format('Y-m') }}">

                    <button type="submit" class="btn btn-primary mr-2">Cari</button>

                    <a id="cetak"
                        href="{{ route('laporan.laporan_bulanan', ['bulan' => (request('bulan') ?? $bulan), 'pdf' => true]) }}"
                        class="btn btn-success text-nowrap px-4 {{ (request('bulan') ?? $bulan) ? '' : 'disabled-link' }}">
                        Cetak PDF
                    </a>
                </form>
            </div>
            <div class="table-responsive mt-2">
                <table class="table table-bordered table-sm">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>No Transaksi</th>
                            <th>Waktu</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th>HPP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp

                        @foreach($reportData as $trx)
                            {{-- DETAIL PRODUK --}}
                            @foreach($trx['details'] as $detail)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ $trx['transaction_code'] }}</td>
                                    <td class="text-center">{{ $trx['waktu'] }}</td>
                                    <td>{{ $detail->product->name }}</td>
                                    <td class="text-center">{{ $detail->quantity }}</td>
                                    <td class="text-end">
                                        Rp {{ number_format($detail->price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        Rp {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        Rp {{ number_format($detail->price_buy * $detail->quantity, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach

                            {{-- TOTAL PER TRANSAKSI --}}
                            <tr class="table-secondary fw-bold">
                                <td colspan="4" class="text-end">TOTAL TRANSAKSI</td>
                                <td class="text-center">{{ $trx['totalQty'] }}</td>
                                <td></td>
                                <td class="text-end">
                                    Rp {{ number_format($trx['omzetKotor'], 0, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($trx['totalHPP'], 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="6" class="text-end">Diskon Produk</td>
                                <td colspan="2" class="text-end">
                                    Rp {{ number_format($trx['diskonProduk'], 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="6" class="text-end">Voucher</td>
                                <td colspan="2" class="text-end">
                                    Rp {{ number_format($trx['voucher'], 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="6" class="text-end">Pajak</td>
                                <td colspan="2" class="text-end">
                                    Rp {{ number_format($trx['pajak'], 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr class="table-warning fw-bold">
                                <td colspan="6" class="text-end">Omzet Bersih</td>
                                <td colspan="2" class="text-end">
                                    Rp {{ number_format($trx['omzetBersih'], 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr class="table-success fw-bold">
                                <td colspan="6" class="text-end">Keuntungan</td>
                                <td colspan="2" class="text-end">
                                    Rp {{ number_format($trx['keuntungan'], 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <td colspan="8">&nbsp;</td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- GRAND TOTAL --}}
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="4" class="text-end">GRAND TOTAL</th>
                            <th class="text-center">{{ $totalAll['totalQty'] }}</th>
                            <th></th>
                            <th class="text-end">
                                Rp {{ number_format($totalAll['omzetKotor'], 0, ',', '.') }}
                            </th>
                            <th class="text-end">
                                Rp {{ number_format($totalAll['totalHPP'], 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr>
                            <th colspan="6" class="text-end">Diskon Produk</th>
                            <th colspan="2" class="text-end">
                                Rp {{ number_format($totalAll['diskonProduk'], 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr>
                            <th colspan="6" class="text-end">Voucher</th>
                            <th colspan="2" class="text-end">
                                Rp {{ number_format($totalAll['voucher'], 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr>
                            <th colspan="6" class="text-end">Pajak</th>
                            <th colspan="2" class="text-end">
                                Rp {{ number_format($totalAll['pajak'], 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr class="table-warning">
                            <th colspan="6" class="text-end">OMZET BERSIH</th>
                            <th colspan="2" class="text-end">
                                Rp {{ number_format($totalAll['omzetBersih'], 0, ',', '.') }}
                            </th>
                        </tr>

                        <tr class="table-success">
                            <th colspan="6" class="text-end">TOTAL KEUNTUNGAN</th>
                            <th colspan="2" class="text-end">
                                Rp {{ number_format($totalAll['keuntungan'], 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection