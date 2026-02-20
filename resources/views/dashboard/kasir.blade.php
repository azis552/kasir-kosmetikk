@extends('template.master')

@section('content')
    <style>
        /* Card KPI berwarna */
        .stat-card {
            border: 0;
            border-radius: 16px;
            color: #fff;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .10);
            transition: transform .15s ease, box-shadow .15s ease;
            min-height: 110px;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 35px rgba(0, 0, 0, .14);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, .18);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .18);
        }

        .stat-card .stat-icon i {
            font-size: 22px;
        }

        .stat-card .stat-label {
            font-size: 12px;
            opacity: .85;
        }

        .stat-card .stat-value {
            font-size: 20px;
            font-weight: 700;
            margin-top: 4px;
        }

        .stat-card .stat-sub {
            font-size: 12px;
            opacity: .85;
            margin-top: 8px;
        }

        /* Gradients */
        .g-primary {
            background: linear-gradient(135deg, #2563eb, #22c55e);
        }

        .g-success {
            background: linear-gradient(135deg, #16a34a, #06b6d4);
        }

        .g-warning {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
        }

        .g-info {
            background: linear-gradient(135deg, #06b6d4, #8b5cf6);
        }

        .g-purple {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
        }

        .g-dark {
            background: linear-gradient(135deg, #111827, #334155);
        }

        /* Panel card biasa biar lebih modern */
        .soft-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
        }

        /* Table lebih clean */
        .table thead th {
            background: #f8fafc;
            border-bottom: 0;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
        }

        .table tbody td {
            vertical-align: middle;
        }

        .badge-soft {
            border-radius: 999px;
            padding: .35rem .6rem;
            font-weight: 700;
        }
    </style>

    @php
        $rupiah = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    @endphp

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Dashboard Kasir</h4>
                <small class="text-muted">Ringkasan shift & transaksi kamu hari ini</small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('kasir.index') }}" class="btn btn-primary text-nowrap">Transaksi Baru</a>
                <a href="{{ route('transaksis.riwayat') }}" class="btn btn-outline-secondary text-nowrap">Riwayat</a>
            </div>
        </div>

        {{-- KPI --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="stat-card g-primary p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">Penjualan Bersih (Tanpa Pajak)</div>
                            <div class="stat-value">{{ $rupiah($kpi->net_sales_ex_tax) }}</div>
                            <div class="stat-sub">Shift hari ini</div>
                        </div>
                        <div class="stat-icon"><i class="ph ph-cash-register"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="stat-card g-info p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">Transaksi</div>
                            <div class="stat-value">{{ (int) $kpi->trx_count }}</div>
                            <div class="stat-sub">Hari ini</div>
                        </div>
                        <div class="stat-icon"><i class="ph ph-receipt"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card g-purple p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">AOV</div>
                            <div class="stat-value">{{ $rupiah($aov) }}</div>
                            <div class="stat-sub">Rata-rata</div>
                        </div>
                        <div class="stat-icon"><i class="ph ph-calculator"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card g-success p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">Voucher & Pajak</div>
                            <div class="stat-value">{{ $rupiah($kpi->voucher_total) }}</div>
                            <div class="stat-sub">Pajak: {{ $rupiah($kpi->tax_total) }}</div>
                        </div>
                        <div class="stat-icon"><i class="ph ph-ticket"></i></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-3">
            {{-- Chart per jam --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Transaksi per Jam (Hari Ini)</div>
                        <canvas id="hourChart" height="110"></canvas>
                    </div>
                </div>
            </div>

            {{-- Payment breakdown --}}
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Metode Pembayaran</div>
                        <ul class="mb-0">
                            @forelse($paymentBreakdown as $p)
                                <li>{{ $p->payment_method }} — <span class="fw-semibold">{{ (int) $p->cnt }}</span></li>
                            @empty
                                <li class="text-muted">Belum ada transaksi</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Recent transactions --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Transaksi Terakhir</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Kode</th>
                                        <th>Metode</th>
                                        <th>Status</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $t)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d M H:i') }}</td>
                                            <td class="fw-semibold">{{ $t->transaction_code }}</td>
                                            <td>{{ $t->payment_method }}</td>
                                            <td>{{ $t->status }}</td>
                                            <td class="text-end">{{ $rupiah($t->total) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Belum ada transaksi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Low stock alert --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Stok Menipis</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-end">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lowStockList as $x)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $x->name }}</div>
                                                <small class="text-muted">{{ $x->barcode }}</small>
                                            </td>
                                            <td class="text-end">{{ (int) $x->quantity }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">Tidak ada stok menipis</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Laporkan ke admin untuk restock.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($hourLabels);
        const counts = @json($hourCounts);

        new Chart(document.getElementById('hourChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{ label: 'Jumlah Transaksi', data: counts }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endsection