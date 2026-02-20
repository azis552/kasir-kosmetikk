@extends('template.master')

@section('content')
    @php
        $rupiah = function ($n) {
            return 'Rp ' . number_format((float) $n, 0, ',', '.');
        };

        $net = (float) ($summary->net_sales_ex_tax ?? 0);
        $gp = (float) ($summary->gross_profit ?? 0);
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
                        <div class="col-auto">
                            <button class="btn btn-primary">Cari</button>
                        </div>
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
                        <div class="col-auto">
                            <button class="btn btn-primary">Cari</button>
                        </div>
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
                        <div class="col-auto">
                            <button class="btn btn-primary">Cari</button>
                        </div>

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

        {{-- SUMMARY --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Penjualan Bersih (Tanpa Pajak)</div>
                        <div class="h5 mb-0">{{ $rupiah($summary->net_sales_ex_tax ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">HPP (COGS)</div>
                        <div class="h5 mb-0">{{ $rupiah($summary->cogs ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Laba Kotor</div>
                        <div class="h5 mb-0">{{ $rupiah($summary->gross_profit ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Margin</div>
                        <div class="h5 mb-0">{{ number_format($margin, 2) }}%</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- EXTRA INFO --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Diskon Item</div>
                        <div class="h6 mb-0">{{ $rupiah($summary->item_discount ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Voucher</div>
                        <div class="h6 mb-0">{{ $rupiah($summary->voucher_total ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Pajak</div>
                        <div class="h6 mb-0">{{ $rupiah($summary->tax_total ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Penjualan Kotor</div>
                        <div class="h6 mb-0">{{ $rupiah($summary->gross_sales ?? 0) }}</div>
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
                                <th class="text-end">Diskon Item</th>
                                <th class="text-end">Voucher</th>
                                <th class="text-end">Pajak</th>
                                <th class="text-end">Penjualan Bersih (Tanpa Pajak)</th>
                                <th class="text-end">HPP</th>
                                <th class="text-end">Laba Kotor</th>
                                <th class="text-end">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $r)
                                @php
                                    $netRow = (float) $r->net_sales_ex_tax;
                                    $gpRow = (float) $r->gross_profit;
                                    $mRow = $netRow > 0 ? ($gpRow / $netRow) * 100 : 0;
                                  @endphp
                                <tr>
                                    <td>{{ $r->period }}</td>
                                    <td class="text-end">{{ $rupiah($r->gross_sales) }}</td>
                                    <td class="text-end">{{ $rupiah($r->item_discount) }}</td>
                                    <td class="text-end">{{ $rupiah($r->voucher_total) }}</td>
                                    <td class="text-end">{{ $rupiah($r->tax_total) }}</td>
                                    <td class="text-end">{{ $rupiah($r->net_sales_ex_tax) }}</td>
                                    <td class="text-end">{{ $rupiah($r->cogs) }}</td>
                                    <td class="text-end">{{ $rupiah($r->gross_profit) }}</td>
                                    <td class="text-end">{{ number_format($mRow, 2) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Tidak ada data pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
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
                if (mode === 'harian') {
                    url = "{{ route('laporan.laba_rugi_harian') }}" + `?tgl=${encodeURIComponent(val)}&pdf=true`;
                } else if (mode === 'bulanan') {
                    url = "{{ route('laporan.laba_rugi_bulanan') }}" + `?bulan=${encodeURIComponent(val)}&pdf=true`;
                } else {
                    url = "{{ route('laporan.laba_rugi_tahunan') }}" + `?tahun=${encodeURIComponent(val)}&pdf=true`;
                }
                $('#cetak').attr('href', url);
            }

            const inputId = mode === 'harian' ? '#tgl' : (mode === 'bulanan' ? '#bulan' : '#tahun');

            $(document).on('input change', inputId, function () {
                const v = $(this).val();
                if (v) {
                    $('#cetak').removeClass('disabled-link');
                    setHref(v);
                } else {
                    $('#cetak').addClass('disabled-link').attr('href', '#');
                }
            });

            // init
            const initVal = $(inputId).val();
            if (initVal) { setHref(initVal); }
        })();
    </script>

@endpush