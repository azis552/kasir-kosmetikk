@php
$rupiah = function ($n) {
  return 'Rp ' . number_format((float) $n, 0, ',', '.');
};

$net = (float) ($summary->net_sales_ex_tax ?? 0);
$gp = (float) ($summary->gross_profit ?? 0);
$margin = $net > 0 ? ($gp / $net) * 100 : 0;
@endphp

<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>{{ $title }}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 11px;
    }

    .title {
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 2px;
    }

    .sub {
      color: #555;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #999;
      padding: 6px;
    }

    th {
      background: #f2f2f2;
    }

    .right {
      text-align: right;
    }

    .nowrap {
      white-space: nowrap;
    }

    .summary {
      margin-bottom: 10px;
    }

    .summary td {
      border: none;
      padding: 2px 0;
    }
  </style>
</head>

<body>
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
          {{ $title }}
        </div>
        <div class="sub" style="margin:0; padding:0; line-height:1.2;">
          Periode: {{ $periodeText }}
        </div>
      </td>
    </tr>
  </table>



  <table class="summary">
    <tr>
      <td class="nowrap">Penjualan Bersih (Tanpa Pajak)</td>
      <td class="right">{{ $rupiah($summary->net_sales_ex_tax ?? 0) }}</td>
    </tr>
    <tr>
      <td class="nowrap">HPP (COGS)</td>
      <td class="right">{{ $rupiah($summary->cogs ?? 0) }}</td>
    </tr>
    <tr>
      <td class="nowrap">Laba Kotor</td>
      <td class="right">{{ $rupiah($summary->gross_profit ?? 0) }}</td>
    </tr>
    <tr>
      <td class="nowrap">Margin</td>
      <td class="right">{{ number_format($margin, 2) }}%</td>
    </tr>
    <tr>
      <td class="nowrap">Diskon Item</td>
      <td class="right">{{ $rupiah($summary->item_discount ?? 0) }}</td>
    </tr>
    <tr>
      <td class="nowrap">Voucher</td>
      <td class="right">{{ $rupiah($summary->voucher_total ?? 0) }}</td>
    </tr>
    <tr>
      <td class="nowrap">Pajak</td>
      <td class="right">{{ $rupiah($summary->tax_total ?? 0) }}</td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th>Periode</th>
        <th class="right">Penjualan Kotor</th>
        <th class="right">Diskon Item</th>
        <th class="right">Voucher</th>
        <th class="right">Pajak</th>
        <th class="right">Penjualan Bersih (Tanpa Pajak)</th>
        <th class="right">HPP</th>
        <th class="right">Laba Kotor</th>
        <th class="right">Margin</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        @php
  $netRow = (float) $r->net_sales_ex_tax;
  $gpRow = (float) $r->gross_profit;
  $mRow = $netRow > 0 ? ($gpRow / $netRow) * 100 : 0;
        @endphp
        <tr>
          <td class="nowrap">{{ $r->period }}</td>
          <td class="right">{{ $rupiah($r->gross_sales) }}</td>
          <td class="right">{{ $rupiah($r->item_discount) }}</td>
          <td class="right">{{ $rupiah($r->voucher_total) }}</td>
          <td class="right">{{ $rupiah($r->tax_total) }}</td>
          <td class="right">{{ $rupiah($r->net_sales_ex_tax) }}</td>
          <td class="right">{{ $rupiah($r->cogs) }}</td>
          <td class="right">{{ $rupiah($r->gross_profit) }}</td>
          <td class="right">{{ number_format($mRow, 2) }}%</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>