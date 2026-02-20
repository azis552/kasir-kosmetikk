<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard.admin');
        }

        return redirect()->route('dashboard.kasir');
    }

    public function admin(Request $request)
    {
        // range chart: 7 atau 30 hari
        $range = (int) $request->get('range', 30);
        $range = in_array($range, [7, 30]) ? $range : 30;

        $todayStart = now()->startOfDay();
        $todayEnd   = now()->endOfDay();

        $from = now()->subDays($range - 1)->startOfDay();
        $to   = now()->endOfDay();

        // === 1) KPI Hari Ini (header transaksi) ===
        $trxToday = DB::table('transactions as t')
            ->whereBetween('t.transaction_date', [$todayStart, $todayEnd])
            ->where('t.status', 'paid');

        $kpi = (clone $trxToday)
            ->selectRaw('COALESCE(SUM(t.subtotal - COALESCE(t.potongan_voucher,0)),0) as net_sales_ex_tax')
            ->selectRaw('COALESCE(COUNT(t.id),0) as trx_count')
            ->selectRaw('COALESCE(SUM(COALESCE(t.tax_amount,0)),0) as tax_total')
            ->selectRaw('COALESCE(SUM(COALESCE(t.potongan_voucher,0)),0) as voucher_total')
            ->first();

        // === 2) KPI Profit Hari Ini (butuh COGS dari detail; hindari double count header) ===
        $detailAgg = DB::table('transaction_details')
            ->selectRaw('transaction_id')
            ->selectRaw('SUM(quantity * price_buy) as cogs')
            ->groupBy('transaction_id');

        $profitToday = DB::table('transactions as t')
            ->joinSub($detailAgg, 'd', fn($j) => $j->on('d.transaction_id', '=', 't.id'))
            ->whereBetween('t.transaction_date', [$todayStart, $todayEnd])
            ->where('t.status', 'paid')
            ->selectRaw('COALESCE(SUM((t.subtotal - COALESCE(t.potongan_voucher,0)) - d.cogs),0) as gross_profit')
            ->selectRaw('COALESCE(SUM(d.cogs),0) as cogs_total')
            ->first();

        $aov = ($kpi->trx_count ?? 0) > 0 ? ($kpi->net_sales_ex_tax / $kpi->trx_count) : 0;
        $margin = ($kpi->net_sales_ex_tax ?? 0) > 0 ? (($profitToday->gross_profit / $kpi->net_sales_ex_tax) * 100) : 0;

        // === 3) Chart Sales & Profit (7/30 hari) ===
        $chartRows = DB::table('transactions as t')
            ->joinSub($detailAgg, 'd', fn($j) => $j->on('d.transaction_id', '=', 't.id'))
            ->whereBetween('t.transaction_date', [$from, $to])
            ->where('t.status', 'paid')
            ->selectRaw('DATE(t.transaction_date) as day')
            ->selectRaw('COALESCE(SUM(t.subtotal - COALESCE(t.potongan_voucher,0)),0) as net_sales_ex_tax')
            ->selectRaw('COALESCE(SUM((t.subtotal - COALESCE(t.potongan_voucher,0)) - d.cogs),0) as gross_profit')
            ->groupByRaw('DATE(t.transaction_date)')
            ->orderBy('day')
            ->get();

        // isi missing dates agar grafik rapi
        $map = $chartRows->keyBy('day');
        $labels = [];
        $sales = [];
        $profits = [];

        for ($i = 0; $i < $range; $i++) {
            $day = $from->copy()->addDays($i)->toDateString();
            $labels[] = $day;
            $sales[] = (float) ($map[$day]->net_sales_ex_tax ?? 0);
            $profits[] = (float) ($map[$day]->gross_profit ?? 0);
        }

        // === 4) Top Produk (30 hari) ===
        $topProducts = DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('products as p', 'p.id', '=', 'td.product_id')
            ->whereBetween('t.transaction_date', [$from, $to])
            ->where('t.status', 'paid')
            ->groupBy('td.product_id', 'p.name')
            ->selectRaw('p.name')
            ->selectRaw('SUM(td.quantity) as qty')
            ->selectRaw('SUM(td.line_total) as omzet')
            ->selectRaw('SUM(td.line_total - (td.quantity * td.price_buy)) as profit')
            ->orderByDesc('omzet')
            ->limit(10)
            ->get();

        // === 5) Stok (stocklevels + products.min_stock) ===
        $lowStockCount = DB::table('stocklevels as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->where('p.is_active', 1)
            ->whereColumn('s.quantity', '<=', 'p.min_stock')
            ->count();

        $outOfStockCount = DB::table('stocklevels as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->where('p.is_active', 1)
            ->where('s.quantity', '<=', 0)
            ->count();

        $lowStockList = DB::table('stocklevels as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->where('p.is_active', 1)
            ->whereColumn('s.quantity', '<=', 'p.min_stock')
            ->select('p.name', 'p.barcode', 'p.min_stock', 's.quantity')
            ->orderBy('s.quantity')
            ->limit(7)
            ->get();

        // === 6) Mutasi stok hari ini (stockmovements) ===
        $stockMoveToday = DB::table('stockmovements')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw("movement_type")
            ->selectRaw("SUM(change_amount) as total_change")
            ->groupBy('movement_type')
            ->get()
            ->keyBy('movement_type');

        $stockIn  = (float) ($stockMoveToday['in']->total_change ?? 0);
        $stockOut = (float) ($stockMoveToday['OUT']->total_change ?? 0);

        $recentStockMoves = DB::table('stockmovements as sm')
            ->join('products as p', 'p.id', '=', 'sm.product_id')
            ->select('sm.created_at', 'sm.movement_type', 'sm.change_amount', 'sm.supplier', 'sm.ref_nota', 'p.name')
            ->orderByDesc('sm.created_at')
            ->limit(8)
            ->get();

        // === 7) Promo (diskon_produks & vouchers) ===
        $activeVouchers = DB::table('vouchers')
            ->where('is_active', 1)
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->select('code', 'discount_amount', 'uses', 'max_uses', 'end_date')
            ->orderBy('end_date')
            ->limit(6)
            ->get();

        $activeDiskon = DB::table('diskon_produks as dp')
            ->join('products as p', 'p.id', '=', 'dp.product_id')
            ->where('dp.is_active', 1)
            ->whereDate('dp.start_date', '<=', now()->toDateString())
            ->whereDate('dp.end_date', '>=', now()->toDateString())
            ->select('p.name', 'dp.diskon_percentage', 'dp.diskon_amount', 'dp.min_qty', 'dp.end_date')
            ->orderBy('dp.end_date')
            ->limit(6)
            ->get();

        // === 8) Transaksi terbaru (global) ===
        $recentTransactions = DB::table('transactions as t')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->whereBetween('t.transaction_date', [$todayStart, $todayEnd])
            ->orderByDesc('t.transaction_date')
            ->select('t.transaction_code', 't.transaction_date', 't.total', 't.status', 't.payment_method', 'u.name as cashier')
            ->limit(10)
            ->get();

        $title = 'Dashboard';

        return view('dashboard.admin', compact(
            'range', 'kpi', 'profitToday', 'aov', 'margin',
            'labels', 'sales', 'profits',
            'topProducts',
            'lowStockCount', 'outOfStockCount', 'lowStockList',
            'stockIn', 'stockOut', 'recentStockMoves',
            'activeVouchers', 'activeDiskon',
            'title',
            'recentTransactions'

        ));
    }

    public function kasir(Request $request)
    {
        $userId = $request->user()->id;

        $todayStart = now()->startOfDay();
        $todayEnd   = now()->endOfDay();

        // KPI kasir hari ini
        $trxToday = DB::table('transactions as t')
            ->whereBetween('t.transaction_date', [$todayStart, $todayEnd])
            ->where('t.status', 'paid')
            ->where('t.user_id', $userId);

        $kpi = (clone $trxToday)
            ->selectRaw('COALESCE(SUM(t.subtotal - COALESCE(t.potongan_voucher,0)),0) as net_sales_ex_tax')
            ->selectRaw('COALESCE(COUNT(t.id),0) as trx_count')
            ->selectRaw('COALESCE(SUM(COALESCE(t.potongan_voucher,0)),0) as voucher_total')
            ->selectRaw('COALESCE(SUM(COALESCE(t.tax_amount,0)),0) as tax_total')
            ->first();

        $aov = ($kpi->trx_count ?? 0) > 0 ? ($kpi->net_sales_ex_tax / $kpi->trx_count) : 0;

        // Metode pembayaran kasir hari ini
        $paymentBreakdown = (clone $trxToday)
            ->selectRaw('payment_method, COUNT(*) as cnt')
            ->groupBy('payment_method')
            ->orderByDesc('cnt')
            ->get();

        // Transaksi per jam (kasir) hari ini
        $hourRows = (clone $trxToday)
            ->selectRaw('HOUR(t.transaction_date) as hour')
            ->selectRaw('COUNT(*) as cnt')
            ->groupByRaw('HOUR(t.transaction_date)')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $hourLabels = [];
        $hourCounts = [];
        for ($h = 0; $h <= 23; $h++) {
            $hourLabels[] = sprintf('%02d:00', $h);
            $hourCounts[] = (int) ($hourRows[$h]->cnt ?? 0);
        }

        // Recent transaksi kasir
        $recentTransactions = DB::table('transactions')
            ->where('user_id', $userId)
            ->orderByDesc('transaction_date')
            ->select('transaction_code', 'transaction_date', 'total', 'status', 'payment_method')
            ->limit(12)
            ->get();

        // Alert stok menipis (ringkas)
        $lowStockList = DB::table('stocklevels as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->where('p.is_active', 1)
            ->whereColumn('s.quantity', '<=', 'p.min_stock')
            ->select('p.name', 'p.barcode', 'p.min_stock', 's.quantity')
            ->orderBy('s.quantity')
            ->limit(6)
            ->get();

        $title = 'Dashboard';

        return view('dashboard.kasir', compact(
            'kpi', 'aov',
            'paymentBreakdown',
            'title',
            'hourLabels', 'hourCounts',
            'recentTransactions',
            'lowStockList'
        ));
    }
}
