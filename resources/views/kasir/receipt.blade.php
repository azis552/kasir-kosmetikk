<!DOCTYPE html>
<html>
<head>
    <title>Struk</title>

    <style>
        @page {
            size: 58mm auto;
            margin: 0;
top-margin:0;
        }

        body {
            width: 52mm;              /* 🔴 AMAN: tidak melebar */
            margin: 0;
margin-right: 1mm;
	    margin-left:0.2mm;
            padding: 0;

            font-family: arial;
            font-size: 11px;
            line-height: 1.1;
        }

        * {
            box-sizing: border-box;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }

        .line {
            border-top: 1px dashed #000;
            margin: 1px 0;
        }

        /* ===== ROW TANPA TABLE ===== */
        .row {
            width: 100%;
            clear: both;
        }

        .left {
            float: left;
            width: 60%;
            white-space: nowrap;
            overflow: hidden;
        }

        .right {
            float: right;
            width: 40%;
            text-align: right;
            white-space: nowrap;
margin-right: 2mm;
        }

        .clear {
            clear: both;
        }

        .item-name {
	    margin-left : 5mm;
            font-weight: bold;
        }

        @media print {
    .no-print {
        display: none !important;
    }
}
    </style>
</head>
<button class="no-print" onclick="window.print()">Print</button>

<body onload="window.print()">

<!-- ===== HEADER ===== -->
<div class="center">
    @if(store_logo('app_light'))
        <img src="{{ store_logo('app_light') }}" style="max-width:25mm;">
    @endif

    <div class="bold">{{ strtoupper(store_name()) }}</div>
    <div style="margin-left:2mm">{{ store_address() }}</div>
    <div>{{ store_phone() }}</div>
    <div>{{ store_email() }}</div>

    @if(store_receipt_header())
        <div>{{ store_receipt_header() }}</div>
    @endif
</div>

<div class="line"></div>

<!-- ===== INFO TRANSAKSI ===== -->
<table style="margin-left:2mm;margin-right:2mm;width:100%; border-collapse:collapse; font-size:11px;">
    <tr>
        <td class="item-name" style="padding:0;">No</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ $transaction->transaction_code }}
        </td>
    </tr>
    <tr>
        <td class="item-name" style="padding:0;">Pelanggan</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ $transaction->pelanggan_name ?? '-' }}
        </td>
    </tr>
    <tr>
        <td class="item-name" style="padding:0;">Tanggal</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ $transaction->created_at->format('d-m-Y H:i') }}
        </td>
    </tr>
</table>

<div class="line"></div>

<!-- ===== ITEM ===== -->
@foreach($details as $detail)
<table style="margin-left:2mm;margin-right:5mm;width:100%; border-collapse:collapse; font-size:11px;">
    <tr>
        <td colspan="2" class="item-name" style="padding:0; font-weight:bold;">
            {{ $detail->product->name }}
        </td>
    </tr>
    <tr>
        <td style="padding:0;">
            {{ $detail->quantity }} x {{ number_format($detail->price,0,',','.') }}
        </td>
        <td style="padding-right:2mm; text-align:right;">
            {{ number_format($detail->line_total,0,',','.') }}
        </td>
    </tr>
</table>
@endforeach

<div class="line"></div>

<!-- ===== TOTAL ===== -->
<table style="margin-left:2mm;margin-right:2mm;width:100%; border-collapse:collapse; font-size:11px;">
    <tr>
        <td class="item-name" style="padding:0;">Subtotal</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ number_format($transaction->subtotal,0,',','.') }}
        </td>
    </tr>
    <tr>
        <td class="item-name" style="padding:0;">Diskon</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ number_format($transaction->diskon_item,0,',','.') }}
        </td>
    </tr>
    <tr>
        <td class="item-name" style="padding:0;">Voucher</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ number_format($transaction->potongan_voucher,0,',','.') }}
        </td>
    </tr>
    <tr style="font-weight:bold;">
        <td class="item-name" style="padding:0;">Total</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ number_format($transaction->total,0,',','.') }}
        </td>
    </tr>
    <tr>
        <td class="item-name" style="padding:0;">Dibayar</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ number_format($transaction->dibayar,0,',','.') }}
        </td>
    </tr>
    <tr>
        <td class="item-name" style="padding:0;">Kembalian</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ number_format($transaction->kembalian,0,',','.') }}
        </td>
    </tr>
    <tr>
        <td class="item-name" style="padding:0;">Metode</td>
        <td style="padding-right:2mm; text-align:right;">
            {{ strtoupper($transaction->payment_method) }}
        </td>
    </tr>
</table>

<div class="line"></div>

<!-- ===== FOOTER ===== -->
<div class="center">
    @if(store_receipt_footer())
        {{ store_receipt_footer() }}<br>
    @endif
    Terima kasih atas kunjungan Anda
</div>

</body>
</html>