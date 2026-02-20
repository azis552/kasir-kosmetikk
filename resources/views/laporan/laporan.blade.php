<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-header img {
            max-width: 80px;
            /* Mengatur ukuran logo */
            margin-bottom: 10px;
        }

        .print-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .total-row td {
            background-color: #e2e2e2;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="print-header">
        @php
            // ambil path logo dokumen (lebih cocok untuk PDF)
            $logoRel = store_logo_path('doc'); // ex: settings/logo_doc.png
            $logoAbs = $logoRel ? public_path('storage/' . $logoRel) : null;
          @endphp

        <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
            <tr>
                <td style="width:90px; vertical-align:middle;">
                    @if($logoAbs && file_exists($logoAbs))
                        <img src="{{ $logoAbs }}" style="height:55px; width:auto; display:block;" alt="Logo">
                    @endif
                </td>

                <td style="vertical-align:middle; text-align:center;">
                    <div class="title" style="margin:0; padding:0; line-height:1.2;">
                        @isset($tgl)
                            <h2>Laporan Harian {{ $tgl }}</h2>
                        @endisset
                        @isset($bulan)
                            <h2>Laporan Bulanan {{ $bulan }}</h2>
                        @endisset
                        @isset($tahun)
                            <h2>Laporan Tahunan {{ $tahun  }}</h2>
                        @endisset
                    </div>

                </td>
            </tr>
        </table>

    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Transaksi</th>
                <th>Waktu</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga Jual</th>
                <th>Subtotal Produk</th>
                <th>HPP</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($reportData as $transaction)

                {{-- DETAIL PRODUK --}}
                @foreach ($transaction['details'] as $item)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $transaction['details']->count() }}">
                                {{ $transaction['transaction_code'] }}
                            </td>
                            <td rowspan="{{ $transaction['details']->count() }}">
                                {{ $transaction['waktu'] }}
                            </td>
                        @endif

                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ App\Helpers\FormatHelper::formatRupiah($item->price) }}</td>
                        <td>{{ App\Helpers\FormatHelper::formatRupiah($item->price * $item->quantity) }}</td>
                        <td>{{ App\Helpers\FormatHelper::formatRupiah($item->price_buy * $item->quantity) }}</td>
                    </tr>
                @endforeach

                {{-- TOTAL PER TRANSAKSI --}}
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL TRANSAKSI</strong></td>
                    <td><strong>{{ $transaction['totalQty'] }}</strong></td>
                    <td></td>
                    <td><strong>{{ App\Helpers\FormatHelper::formatRupiah($transaction['omzetKotor']) }}</strong></td>
                    <td><strong>{{ App\Helpers\FormatHelper::formatRupiah($transaction['totalHPP']) }}</strong></td>
                </tr>

                <tr>
                    <td colspan="5" style="text-align:right;">Diskon Produk</td>
                    <td colspan="2">
                        {{ App\Helpers\FormatHelper::formatRupiah($transaction['diskonProduk']) }}
                    </td>
                </tr>

                <tr>
                    <td colspan="5" style="text-align:right;">Voucher</td>
                    <td colspan="2">
                        {{ App\Helpers\FormatHelper::formatRupiah($transaction['voucher']) }}
                    </td>
                </tr>

                <tr>
                    <td colspan="5" style="text-align:right;">Pajak</td>
                    <td colspan="2">
                        {{ App\Helpers\FormatHelper::formatRupiah($transaction['pajak']) }}
                    </td>
                </tr>

                <tr class="total-row">
                    <td colspan="5" style="text-align:right;">OMZET BERSIH</td>
                    <td colspan="2">
                        {{ App\Helpers\FormatHelper::formatRupiah($transaction['omzetBersih']) }}
                    </td>
                </tr>

                <tr class="total-row">
                    <td colspan="5" style="text-align:right;">KEUNTUNGAN</td>
                    <td colspan="2">
                        {{ App\Helpers\FormatHelper::formatRupiah($transaction['keuntungan']) }}
                    </td>
                </tr>

            @endforeach
        </tbody>

        {{-- GRAND TOTAL --}}
        <tfoot>
            <tr class="total-row">
                <th colspan="3">TOTAL KESELURUHAN</th>
                <th>{{ $totalAll['totalQty'] }}</th>
                <th></th>
                <th>{{ App\Helpers\FormatHelper::formatRupiah($totalAll['omzetKotor']) }}</th>
                <th>{{ App\Helpers\FormatHelper::formatRupiah($totalAll['totalHPP']) }}</th>
            </tr>

            <tr>
                <th colspan="5" style="text-align:right;">Diskon Produk</th>
                <th colspan="2">{{ App\Helpers\FormatHelper::formatRupiah($totalAll['diskonProduk']) }}</th>
            </tr>

            <tr>
                <th colspan="5" style="text-align:right;">Voucher</th>
                <th colspan="2">{{ App\Helpers\FormatHelper::formatRupiah($totalAll['voucher']) }}</th>
            </tr>

            <tr>
                <th colspan="5" style="text-align:right;">Pajak</th>
                <th colspan="2">{{ App\Helpers\FormatHelper::formatRupiah($totalAll['pajak']) }}</th>
            </tr>

            <tr class="total-row">
                <th colspan="5" style="text-align:right;">OMZET BERSIH</th>
                <th colspan="2">{{ App\Helpers\FormatHelper::formatRupiah($totalAll['omzetBersih']) }}</th>
            </tr>

            <tr class="total-row">
                <th colspan="5" style="text-align:right;">TOTAL KEUNTUNGAN</th>
                <th colspan="2">{{ App\Helpers\FormatHelper::formatRupiah($totalAll['keuntungan']) }}</th>
            </tr>
        </tfoot>
    </table>

</body>

</html>