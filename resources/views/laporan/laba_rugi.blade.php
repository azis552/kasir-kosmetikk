@extends('template.master')

@section('content')
@php
    $rupiah = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $net    = (float) ($summary->net_sales_ex_tax ?? 0);
    $gp     = (float) ($summary->gross_profit ?? 0);
    $margin = $net > 0 ? ($gp / $net) * 100 : 0;
@endphp

<div class="container">

    <h4 class="mb-3">Laporan Laba Rugi (Laba Kotor)</h4>

    {{-- FILTER --}}
    <div class="card mb-3">
        <div class="card-body">
            @if($mode === 'harian')
                <form class="row g-2 align-items-center" method="GET" action="{{ route('laporan.laba_rugi_harian') }}">
                    <div class="col-md-4">
                        <input type="date" name="tgl" class="form-control" value="{{ $tgl }}">
                    </div>
                    <div class="col-auto"><button class="btn btn-primary">Cari</button></div>
                    <div class="col-auto">
                        <a id="cetak" class="btn btn-success text-nowrap px-4 {{ $tgl ? '' : 'disabled-link' }}"
                           href="{{ route('laporan.laba_rugi_harian', ['tgl' => $tgl, 'pdf' => true]) }}">
                            Cetak PDF
                        </a>
                    </div>
                </form>
            @elseif($mode === 'bulanan')
                <form class="row g-2 align-items-center" method="GET" action="{{ route('laporan.laba_rugi_bulanan') }}">
                    <div class="col-md-4">
                        <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
                    </div>
                    <div class="col-auto"><button class="btn btn-primary">Cari</button></div>
                    <div class="col-auto">
                        <a id="cetak" class="btn btn-success text-nowrap px-4 {{ $bulan ? '' : 'disabled-link' }}"
                           href="{{ route('laporan.laba_rugi_bulanan', ['bulan' => $bulan, 'pdf' => true]) }}">
                            Cetak PDF
                        </a>
                    </div>
                </form>
            @else
                <form class="row g-2 align-items-center" method="GET" action="{{ route('laporan.laba_rugi_tahunan') }}">
                    <div class="col-md-4">
                        <input type="number" name="tahun" class="form-control" min="2000" max="2100" value="{{ $tahun }}">
                    </div>
                    <div class="col-auto"><button class="btn btn-primary">Cari</button></div>
                    <div class="col-auto">
                        <a id="cetak" class="btn btn-success text-nowrap px-4 {{ $tahun ? '' : 'disabled-link' }}"
                           href="{{ route('laporan.laba_rugi_tahunan', ['tahun' => $tahun, 'pdf' => true]) }}">
                            Cetak PDF
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════
         KETERANGAN RUMUS
    ══════════════════════════════════════ --}}
    <div class="card mb-3 border-0 bg-light">
        <div class="card-body py-2">
            <div class="d-flex align-items-center mb-1" style="cursor:pointer;"
                 data-toggle="collapse" data-target="#panelRumusLR">
                <i class="ph ph-info mr-1 text-primary"></i>
                <span class="fw-semibold text-primary" style="font-size:13px;">
                    Keterangan Rumus Perhitungan
                </span>
                <i class="ph ph-caret-down ml-1 text-primary" style="font-size:12px;"></i>
            </div>
            <div id="panelRumusLR" class="collapse show">
                <div class="row" style="font-size:12px;">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-nowrap">Penjualan Kotor</td>
                                    <td>=</td>
                                    <td class="text-muted">Harga Jual × Qty (sebelum diskon)</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-nowrap">Diskon Item</td>
                                    <td>=</td>
                                    <td class="text-muted">Total potongan diskon per item</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-nowrap">Voucher</td>
                                    <td>=</td>
                                    <td class="text-muted">Potongan kode voucher</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-nowrap">Penjualan Bersih</td>
                                    <td>=</td>
                                    <td class="text-muted">Total − Pajak</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-nowrap">HPP (COGS)</td>
                                    <td>=</td>
                                    <td class="text-muted">Harga Beli × Qty (modal)</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-nowrap">Laba Kotor</td>
                                    <td>=</td>
                                    <td class="text-muted">Penjualan Bersih − HPP</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-nowrap">Margin</td>
                                    <td>=</td>
                                    <td class="text-muted">Laba Kotor ÷ Penjualan Bersih × 100%</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-nowrap">Pajak</td>
                                    <td>=</td>
                                    <td class="text-muted">Informasi saja, tidak mengurangi laba</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="alert alert-info py-1 px-2 mb-0 mt-1" style="font-size:11px;">
                            <i class="ph ph-lightbulb mr-1"></i>
                            <strong>Catatan:</strong> Laporan ini menampilkan <em>laba kotor</em>
                            (belum dikurangi biaya operasional seperti gaji, sewa, listrik, dll).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted" style="font-size:12px;">
                        Penjualan Bersih (Tanpa Pajak)
                        <br><small class="text-muted">= Total − Pajak</small>
                    </div>
                    <div class="h5 mb-0 mt-1">{{ $rupiah($summary->net_sales_ex_tax ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted" style="font-size:12px;">
                        HPP (COGS)
                        <br><small class="text-muted">= Harga Beli × Qty</small>
                    </div>
                    <div class="h5 mb-0 mt-1">{{ $rupiah($summary->cogs ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="text-muted" style="font-size:12px;">
                        Laba Kotor
                        <br><small class="text-muted">= Penjualan Bersih − HPP</small>
                    </div>
                    <div class="h5 mb-0 mt-1 text-success">{{ $rupiah($summary->gross_profit ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted" style="font-size:12px;">
                        Margin
                        <br><small class="text-muted">= Laba Kotor ÷ Penjualan Bersih × 100%</small>
                    </div>
                    <div class="h5 mb-0 mt-1">{{ number_format($margin, 2) }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- EXTRA INFO --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:12px;">Penjualan Kotor <small>(sebelum diskon)</small></div>
                    <div class="h6 mb-0">{{ $rupiah($summary->gross_sales ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:12px;">Diskon Item</div>
                    <div class="h6 mb-0">{{ $rupiah($summary->item_discount ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:12px;">Voucher</div>
                    <div class="h6 mb-0">{{ $rupiah($summary->voucher_total ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:12px;">Pajak <small>(info saja)</small></div>
                    <div class="h6 mb-0">{{ $rupiah($summary->tax_total ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="text-end">Penjualan Kotor</th>
                            <th class="text-end">− Diskon Item</th>
                            <th class="text-end">− Voucher</th>
                            <th class="text-end">Pajak (info)</th>
                            <th class="text-end">Penjualan Bersih</th>
                            <th class="text-end">− HPP</th>
                            <th class="text-end text-success fw-bold">Laba Kotor</th>
                            <th class="text-end">Margin</th>
                        </tr>
                        <tr class="text-muted" style="font-size:10px; background:#f8f9fa;">
                            <td></td>
                            <td class="text-end">Harga Jual × Qty</td>
                            <td class="text-end">Diskon per item</td>
                            <td class="text-end">Kode voucher</td>
                            <td class="text-end">Tidak mengurangi laba</td>
                            <td class="text-end">Total − Pajak</td>
                            <td class="text-end">Harga Beli × Qty</td>
                            <td class="text-end">Bersih − HPP</td>
                            <td class="text-end">Laba ÷ Bersih</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                        @php
                            $netRow = (float) $r->net_sales_ex_tax;
                            $gpRow  = (float) $r->gross_profit;
                            $mRow   = $netRow > 0 ? ($gpRow / $netRow) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $r->period }}</td>
                            <td class="text-end">{{ $rupiah($r->gross_sales) }}</td>
                            <td class="text-end text-danger">{{ $rupiah($r->item_discount) }}</td>
                            <td class="text-end text-danger">{{ $rupiah($r->voucher_total) }}</td>
                            <td class="text-end text-muted">{{ $rupiah($r->tax_total) }}</td>
                            <td class="text-end">{{ $rupiah($r->net_sales_ex_tax) }}</td>
                            <td class="text-end text-danger">{{ $rupiah($r->cogs) }}</td>
                            <td class="text-end fw-bold text-success">{{ $rupiah($r->gross_profit) }}</td>
                            <td class="text-end">{{ number_format($mRow, 2) }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Tidak ada data pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($rows->count() > 1)
                    <tfoot class="table-dark">
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-end">{{ $rupiah($summary->gross_sales ?? 0) }}</th>
                            <th class="text-end">{{ $rupiah($summary->item_discount ?? 0) }}</th>
                            <th class="text-end">{{ $rupiah($summary->voucher_total ?? 0) }}</th>
                            <th class="text-end">{{ $rupiah($summary->tax_total ?? 0) }}</th>
                            <th class="text-end">{{ $rupiah($summary->net_sales_ex_tax ?? 0) }}</th>
                            <th class="text-end">{{ $rupiah($summary->cogs ?? 0) }}</th>
                            <th class="text-end">{{ $rupiah($summary->gross_profit ?? 0) }}</th>
                            <th class="text-end">{{ number_format($margin, 2) }}%</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('script')
<script>
(function () {
    const mode = @json($mode);
    function setHref(val) {
        let url = '';
        if (mode === 'harian')       url = "{{ route('laporan.laba_rugi_harian') }}"  + `?tgl=${encodeURIComponent(val)}&pdf=true`;
        else if (mode === 'bulanan') url = "{{ route('laporan.laba_rugi_bulanan') }}" + `?bulan=${encodeURIComponent(val)}&pdf=true`;
        else                         url = "{{ route('laporan.laba_rugi_tahunan') }}" + `?tahun=${encodeURIComponent(val)}&pdf=true`;
        $('#cetak').attr('href', url);
    }
    const inputId = mode === 'harian' ? '#tgl' : (mode === 'bulanan' ? '#bulan' : '#tahun');
    $(document).on('input change', inputId, function () {
        const v = $(this).val();
        if (v) { $('#cetak').removeClass('disabled-link'); setHref(v); }
        else   { $('#cetak').addClass('disabled-link').attr('href', '#'); }
    });
    const initVal = $(inputId).val();
    if (initVal) { setHref(initVal); }
})();
</script>
@endpush