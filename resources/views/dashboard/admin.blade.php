@extends('template.master')

@section('content')
<style>
    .stat-card {
        border: 0;
        border-radius: 16px;
        color: #fff;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,.10);
        transition: transform .15s ease, box-shadow .15s ease;
        min-height: 110px;
        text-decoration: none;
        display: block;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 35px rgba(0,0,0,.14);
        color: #fff;
        text-decoration: none;
    }
    .stat-card .stat-icon {
        width: 48px; height: 48px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 14px;
        background: rgba(255,255,255,.18);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
        flex-shrink: 0;
    }
    .stat-card .stat-icon i { font-size: 22px; }
    .stat-card .stat-label  { font-size: 12px; opacity: .85; }
    .stat-card .stat-value  { font-size: 20px; font-weight: 700; margin-top: 4px; }
    .stat-card .stat-sub    { font-size: 12px; opacity: .85; margin-top: 8px; }
    .stat-mini              { min-height: 92px; padding: 14px 16px !important; }
    .stat-mini .stat-value  { font-size: 18px; }

    .g-success  { background: linear-gradient(135deg, #16a34a, #06b6d4); }
    .g-warning  { background: linear-gradient(135deg, #f59e0b, #ef4444); }
    .g-danger   { background: linear-gradient(135deg, #dc2626, #7c3aed); }
    .g-info     { background: linear-gradient(135deg, #06b6d4, #8b5cf6); }
    .g-purple   { background: linear-gradient(135deg, #8b5cf6, #ec4899); }
    .g-dark     { background: linear-gradient(135deg, #111827, #334155); }

    .table thead th { background: #f8fafc; border-bottom: 0; font-weight: 700; color: #0f172a; white-space: nowrap; }
    .table tbody td { vertical-align: middle; }

    .card-header-link {
        display: flex; justify-content: space-between; align-items: center;
    }
    .card-header-link a { font-size: 12px; font-weight: 600; }
</style>

@php
    $rupiah = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

<div class="container-fluid">

    {{-- ── HEADER ── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Dashboard Admin</h4>
            <small class="text-muted">Ringkasan performa & operasional</small>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="range" class="form-select" style="width:140px;">
                <option value="7"  {{ $range == 7  ? 'selected' : '' }}>7 Hari</option>
                <option value="30" {{ $range == 30 ? 'selected' : '' }}>30 Hari</option>
            </select>
            <button class="btn btn-primary text-nowrap">Terapkan</button>
        </form>
    </div>

    {{-- ── KPI CARDS ── --}}
    <div class="row g-3 mb-3">
        {{-- Net Sales → laporan harian --}}
        <div class="col-md-3">
            <a href="{{ route('laporan.laporan_harian') }}" class="stat-card g-success p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Net Sales Hari Ini (Tanpa Pajak)</div>
                        <div class="stat-value">{{ $rupiah($kpi->net_sales_ex_tax) }}</div>
                        <div class="stat-sub">Setelah voucher</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-money"></i></div>
                </div>
            </a>
        </div>

        {{-- Laba Kotor → laporan laba rugi harian --}}
        <div class="col-md-3">
            <a href="{{ route('laporan.laba_rugi_harian') }}" class="stat-card g-success p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Laba Kotor Hari Ini</div>
                        <div class="stat-value">{{ $rupiah($profitToday->gross_profit) }}</div>
                        <div class="stat-sub">HPP snapshot dari detail</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-trend-up"></i></div>
                </div>
            </a>
        </div>

        {{-- Transaksi → riwayat transaksi --}}
        <div class="col-md-2">
            <a href="{{ route('transaksis.riwayat') }}" class="stat-card g-success p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Transaksi Hari Ini</div>
                        <div class="stat-value">{{ (int) $kpi->trx_count }}</div>
                        <div class="stat-sub">Paid only</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-receipt"></i></div>
                </div>
            </a>
        </div>

        {{-- AOV → tidak ada halaman khusus, tidak pakai link --}}
        <div class="col-md-2">
            <div class="stat-card g-success p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">AOV</div>
                        <div class="stat-value">{{ $rupiah($aov) }}</div>
                        <div class="stat-sub">Rata-rata transaksi</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-calculator"></i></div>
                </div>
            </div>
        </div>

        {{-- Margin --}}
        <div class="col-md-2">
            <div class="stat-card g-success p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Margin</div>
                        <div class="stat-value">{{ number_format($margin, 2) }}%</div>
                        <div class="stat-sub">Profit / Sales</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-percent"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── INFO CARDS ── --}}
    <div class="row g-3 mb-3">
        {{-- Voucher → halaman voucher --}}
        <div class="col-md-3">
            <a href="{{ route('vouchers.index') }}" class="stat-card g-success stat-mini">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Voucher Hari Ini</div>
                        <div class="stat-value">{{ $rupiah($kpi->voucher_total) }}</div>
                        <div class="stat-sub">Total potongan voucher</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-ticket"></i></div>
                </div>
            </a>
        </div>

        {{-- Pajak → laporan harian --}}
        <div class="col-md-3">
            <a href="{{ route('laporan.laporan_harian') }}" class="stat-card g-success stat-mini">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Pajak Hari Ini</div>
                        <div class="stat-value">{{ $rupiah($kpi->tax_total) }}</div>
                        <div class="stat-sub">Informasi (bukan revenue)</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-receipt"></i></div>
                </div>
            </a>
        </div>

        {{-- Stok Hampir Habis → stock alert filter=low --}}
        <div class="col-md-3">
            <a href="{{ route('products.stock-alert') }}?filter=low" class="stat-card g-warning stat-mini">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Stok Hampir Habis</div>
                        <div class="stat-value">{{ (int) $lowStockCount }} produk</div>
                        <div class="stat-sub">Klik untuk lihat detail →</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-warning-circle"></i></div>
                </div>
            </a>
        </div>

        {{-- Stok Habis → stock alert filter=empty --}}
        <div class="col-md-3">
            <a href="{{ route('products.stock-alert') }}?filter=empty" class="stat-card g-danger stat-mini">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-label">Stok Habis</div>
                        <div class="stat-value">{{ (int) $outOfStockCount }} produk</div>
                        <div class="stat-sub">Klik untuk lihat detail →</div>
                    </div>
                    <div class="stat-icon"><i class="ph ph-x-circle"></i></div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">

        {{-- ── CHART ── --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Tren {{ $range }} Hari: Penjualan & Laba</div>
                    <canvas id="salesProfitChart" height="110"></canvas>
                </div>
            </div>
        </div>

        {{-- ── STOK HAMPIR HABIS PANEL ── --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-link mb-2">
                        <span class="fw-semibold">Stok Hampir Habis (Top)</span>
                        <a href="{{ route('products.stock-alert') }}?filter=low" class="text-warning">
                            Lihat Semua →
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Stok</th>
                                    <th class="text-end">Min</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockList as $x)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $x->name }}</div>
                                        <small class="text-muted">{{ $x->barcode }}</small>
                                    </td>
                                    <td class="text-end">
                                        {{ (int) $x->quantity }}
                                    </td>
                                    <td class="text-end">{{ (int) $x->min_stock }}</td>
                                    <td class="text-end">
                                        {{-- Cari product id berdasarkan barcode --}}
                                        @php
                                            $prod = \App\Models\Products::where('barcode', $x->barcode)->first();
                                        @endphp
                                        @if($prod)
                                        <a href="{{ route('products.stock', $prod->id) }}"
                                           class="btn btn-xs btn-info btn-sm py-0 px-1"
                                           title="Manage Stock">
                                            <i class="ph ph-plus"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada stok menipis</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mutasi stok hari ini</span>
                        <span class="fw-semibold small">
                            <span class="text-success">IN {{ number_format($stockIn) }}</span>
                            &nbsp;|&nbsp;
                            <span class="text-danger">OUT {{ number_format($stockOut) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TOP PRODUK ── --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-link mb-2">
                        <span class="fw-semibold">Top 10 Produk ({{ $range }} Hari)</span>
                        <a href="{{ route('laporan.laporan_harian') }}" class="text-primary">Lihat Laporan →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Omzet</th>
                                    <th class="text-end">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $p)
                                <tr>
                                    <td class="fw-semibold">{{ $p->name }}</td>
                                    <td class="text-end">{{ (int) $p->qty }}</td>
                                    <td class="text-end">{{ $rupiah($p->omzet) }}</td>
                                    <td class="text-end">{{ $rupiah($p->profit) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── PROMO ── --}}
        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-header-link mb-2">
                        <span class="fw-semibold">Voucher Aktif</span>
                        <a href="{{ route('vouchers.index') }}" class="text-primary">Kelola →</a>
                    </div>
                    <ul class="mb-0">
                        @forelse($activeVouchers as $v)
                        <li>
                            <span class="fw-semibold">{{ $v->code }}</span> —
                            {{ $rupiah($v->discount_amount) }},
                            sisa {{ max(0, (int)$v->max_uses - (int)$v->uses) }},
                            sampai {{ \Carbon\Carbon::parse($v->end_date)->format('d M Y') }}
                        </li>
                        @empty
                        <li class="text-muted">Tidak ada voucher aktif</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-header-link mb-2">
                        <span class="fw-semibold">Diskon Produk Aktif</span>
                        <a href="{{ route('diskon.index') }}" class="text-primary">Kelola →</a>
                    </div>
                    <ul class="mb-0">
                        @forelse($activeDiskon as $d)
                        <li>
                            <span class="fw-semibold">{{ $d->name }}</span> —
                            @if(!is_null($d->diskon_percentage))
                                {{ $d->diskon_percentage }}%
                            @else
                                {{ $rupiah($d->diskon_amount) }}
                            @endif
                            (min {{ (int)$d->min_qty }}) sampai
                            {{ \Carbon\Carbon::parse($d->end_date)->format('d M Y') }}
                        </li>
                        @empty
                        <li class="text-muted">Tidak ada diskon aktif</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── TRANSAKSI TERBARU ── --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-link mb-2">
                        <span class="fw-semibold">Transaksi Terbaru (Hari Ini)</span>
                        <a href="{{ route('transaksis.riwayat') }}" class="text-primary">Lihat Semua →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Kode</th>
                                    <th>Kasir</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $t)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('H:i') }}</td>
                                    <td class="fw-semibold">{{ $t->transaction_code }}</td>
                                    <td>{{ $t->cashier }}</td>
                                    <td>{{ $t->payment_method }}</td>
                                    <td>
                                        <span class="badge {{ $t->status == 'PAID' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $t->status }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $rupiah($t->total) }}</td>
                                    <td class="text-end">
                                        @if($t->transaction_code)
                                        @php
                                            $trxId = \App\Models\Transaction::where('transaction_code', $t->transaction_code)->value('id');
                                        @endphp
                                        @if($trxId)
                                        <a href="{{ route('transaksis.show', $trxId) }}"
                                           class="btn btn-sm btn-outline-secondary py-0 px-1">
                                            <i class="ph ph-eye"></i>
                                        </a>
                                        @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('salesProfitChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Net Sales (ex tax)',
                    data: @json($sales),
                    tension: 0.25,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,.20)',
                    fill: true
                },
                {
                    label: 'Gross Profit',
                    data: @json($profits),
                    tension: 0.25,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,.18)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { ticks: { callback: v => new Intl.NumberFormat('id-ID').format(v) } }
            }
        }
    });
</script>
@endsection