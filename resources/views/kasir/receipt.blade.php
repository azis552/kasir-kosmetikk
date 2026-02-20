<!DOCTYPE html>
<html>
<head>
    <title>Struk</title>
    <style>
        body {
            font-family: monospace;
            margin: 0;
            padding: 10px;
            font-size: 12px;
        }

        /* HEADER FLEX */
        .header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .logo img {
            height: 60px;
            object-fit: contain;
        }

        .store-info {
            flex: 1;
            line-height: 1.3;
        }

        .store-name {
            font-weight: bold;
            font-size: 14px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
                padding: 5px;
            }
        }
    </style>
</head>
<body onload="window.print()">

    {{-- HEADER --}}
    <div class="header">

        {{-- LOGO KIRI --}}
        @if(store_logo('receipt'))
            <div class="logo">
                <img src="{{ store_logo('receipt') }}">
            </div>
        @endif

        {{-- HEADER KANAN --}}
        <div class="store-info">
            <div class="store-name">
                {{ strtoupper(store_name()) }}
            </div>

            {{ store_address() }}<br>
            {{ store_phone() }}<br>
            {{ store_email() }}<br>

            @if(store_receipt_header())
                {{ store_receipt_header() }}
            @endif
        </div>

    </div>

    <div class="line"></div>

    {{-- INFO TRANSAKSI --}}
    <table>
        <tr>
            <td>No</td>
            <td>:</td>
            <td>{{ $transaction->transaction_code }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td>:</td>
            <td>{{ $transaction->pelanggan_name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $transaction->created_at->format('d-m-Y H:i') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- ITEM --}}
    @foreach($details as $detail)
        <div>
            <strong>{{ $detail->product->name }}</strong><br>
            {{ $detail->quantity }} x Rp {{ number_format($detail->price,0,',','.') }}
            <span class="right" style="float:right">
                Rp {{ number_format($detail->line_total,0,',','.') }}
            </span>
        </div>
        <div style="clear: both;"></div>
    @endforeach

    <div class="line"></div>

    {{-- TOTAL --}}
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">Rp {{ number_format($transaction->subtotal,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="right">Rp {{ number_format($transaction->diskon_item,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Voucher</td>
            <td class="right">Rp {{ number_format($transaction->potongan_voucher,0,',','.') }}</td>
        </tr>
        <tr>
            <td>PPN {{ $transaction->tax }}%</td>
            <td class="right">Rp {{ number_format($transaction->tax_amount,0,',','.') }}</td>
        </tr>
        <tr>
            <td class="bold">TOTAL</td>
            <td class="right bold">Rp {{ number_format($transaction->total,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Dibayar</td>
            <td class="right">Rp {{ number_format($transaction->dibayar,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="right">Rp {{ number_format($transaction->kembalian,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Metode</td>
            <td class="right">{{ strtoupper($transaction->payment_method) }}</td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- FOOTER --}}
    <div style="text-align:center; margin-top:5px;">
        @if(store_receipt_footer())
            {{ store_receipt_footer() }}<br>
        @endif

        Terima kasih atas kunjungan Anda
    </div>

</body>
</html>
