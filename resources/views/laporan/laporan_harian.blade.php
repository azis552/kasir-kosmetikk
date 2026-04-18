@extends('template.master')

@section('content')
<div class="container">
    <div class="card p-2">
        <div class="card-header">
            <h5>Laporan Harian {{ $tgl }}</h5>
            <form class="d-flex align-items-center mt-3" action="{{ route('laporan.laporan_harian') }}" method="GET">
                <input type="date" name="tgl" class="form-control mr-2"
                    value="{{ request('tgl') ?? $tgl ?? now()->format('Y-m-d') }}">
                <button type="submit" class="btn btn-primary mr-2">Cari</button>
                <a href="{{ route('laporan.laporan_harian', ['tgl' => (request('tgl') ?? $tgl), 'pdf' => true]) }}"
                   class="btn btn-success text-nowrap px-4 {{ (request('tgl') ?? $tgl) ? '' : 'disabled-link' }}">
                    Cetak PDF
                </a>
            </form>
        </div>

        {{-- ══════════════════════════════════════
             KETERANGAN RUMUS
        ══════════════════════════════════════ --}}
        <div class="card mx-2 mt-3 mb-2 border-0 bg-light">
            <div class="card-body py-2">
                <div class="d-flex align-items-center mb-1" style="cursor:pointer;"
                     data-toggle="collapse" data-target="#panelRumus">
                    <i class="ph ph-info mr-1 text-primary"></i>
                    <span class="fw-semibold text-primary" style="font-size:13px;">
                        Keterangan Rumus Perhitungan
                    </span>
                    <i class="ph ph-caret-down ml-1 text-primary" style="font-size:12px;"></i>
                </div>
                <div id="panelRumus" class="collapse show">
                    <div class="row" style="font-size:12px;">
                        <div class="col-md-4">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold text-nowrap">Omzet Kotor</td>
                                        <td>=</td>
                                        <td class="text-muted">Harga Jual × Qty (sebelum diskon)</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-nowrap">Diskon Produk</td>
                                        <td>=</td>
                                        <td class="text-muted">Total potongan diskon per item</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-nowrap">Voucher</td>
                                        <td>=</td>
                                        <td class="text-muted">Potongan dari kode voucher</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold text-nowrap">Omzet Bersih</td>
                                        <td>=</td>
                                        <td class="text-muted">Omzet Kotor − Diskon − Voucher</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-nowrap">HPP</td>
                                        <td>=</td>
                                        <td class="text-muted">Harga Beli × Qty (modal)</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-nowrap">Keuntungan</td>
                                        <td>=</td>
                                        <td class="text-muted">Omzet Bersih − HPP</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-info py-1 px-2 mb-0" style="font-size:11px;">
                                <i class="ph ph-lightbulb mr-1"></i>
                                <strong>Catatan:</strong> Pajak bukan bagian dari keuntungan —
                                ditampilkan sebagai informasi saja. Omzet Bersih sudah
                                <em>tidak</em> termasuk pajak.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

                    @forelse($reportData as $trx)
                        @foreach($trx['details'] as $detail)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $trx['transaction_code'] }}</td>
                                <td class="text-center">{{ $trx['waktu'] }}</td>
                                <td>{{ $item->product_name ?? '-' }}</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($detail->price_buy * $detail->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        <tr class="table-secondary fw-bold">
                            <td colspan="4" class="text-end">TOTAL TRANSAKSI</td>
                            <td class="text-center">{{ $trx['totalQty'] }}</td>
                            <td></td>
                            <td class="text-end">Rp {{ number_format($trx['omzetKotor'], 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($trx['totalHPP'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end text-muted" style="font-size:12px;">
                                Diskon Produk
                                <span class="text-muted ml-1" title="Total potongan diskon per item">ⓘ</span>
                            </td>
                            <td colspan="2" class="text-end">− Rp {{ number_format($trx['diskonProduk'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end text-muted" style="font-size:12px;">
                                Voucher
                                <span class="text-muted ml-1" title="Potongan kode voucher">ⓘ</span>
                            </td>
                            <td colspan="2" class="text-end">− Rp {{ number_format($trx['voucher'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end text-muted" style="font-size:12px;">
                                Pajak
                                <span class="text-muted ml-1" title="Informasi saja, bukan pengurang keuntungan">ⓘ</span>
                            </td>
                            <td colspan="2" class="text-end">Rp {{ number_format($trx['pajak'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-warning fw-bold">
                            <td colspan="6" class="text-end">
                                Omzet Bersih
                                <small class="fw-normal text-muted ml-1">(Kotor − Diskon − Voucher)</small>
                            </td>
                            <td colspan="2" class="text-end">Rp {{ number_format($trx['omzetBersih'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-success fw-bold">
                            <td colspan="6" class="text-end">
                                Keuntungan
                                <small class="fw-normal text-muted ml-1">(Omzet Bersih − HPP)</small>
                            </td>
                            <td colspan="2" class="text-end">Rp {{ number_format($trx['keuntungan'], 0, ',', '.') }}</td>
                        </tr>
                        <tr><td colspan="8">&nbsp;</td></tr>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Tidak ada transaksi pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="table-dark">
                    <tr>
                        <th colspan="4" class="text-end">GRAND TOTAL</th>
                        <th class="text-center">{{ $totalAll['totalQty'] }}</th>
                        <th></th>
                        <th class="text-end">Rp {{ number_format($totalAll['omzetKotor'], 0, ',', '.') }}</th>
                        <th class="text-end">Rp {{ number_format($totalAll['totalHPP'], 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-end fw-normal">− Diskon Produk</th>
                        <th colspan="2" class="text-end">Rp {{ number_format($totalAll['diskonProduk'], 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-end fw-normal">− Voucher</th>
                        <th colspan="2" class="text-end">Rp {{ number_format($totalAll['voucher'], 0, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-end fw-normal">Pajak (info)</th>
                        <th colspan="2" class="text-end">Rp {{ number_format($totalAll['pajak'], 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-warning">
                        <th colspan="6" class="text-end">OMZET BERSIH <small class="fw-normal">(Kotor − Diskon − Voucher)</small></th>
                        <th colspan="2" class="text-end">Rp {{ number_format($totalAll['omzetBersih'], 0, ',', '.') }}</th>
                    </tr>
                    <tr class="table-success">
                        <th colspan="6" class="text-end">TOTAL KEUNTUNGAN <small class="fw-normal">(Omzet Bersih − HPP)</small></th>
                        <th colspan="2" class="text-end">Rp {{ number_format($totalAll['keuntungan'], 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection