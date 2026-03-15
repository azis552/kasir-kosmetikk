<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanLabaRugiController extends Controller
{
    public function harian(Request $request)
    {
        $tgl = $request->get('tgl', now()->toDateString());
        $start = Carbon::parse($tgl)->startOfDay();
        $end   = Carbon::parse($tgl)->endOfDay();

        [$summary, $rows] = $this->buildReport($start, $end, 'day');

        if ($request->boolean('pdf')) {
            return Pdf::loadView('laporan.laba_rugi_pdf', [
                    'title' => 'Laporan Laba Rugi (Harian)',
                    'periodeText' => Carbon::parse($tgl)->format('d M Y'),
                    'summary' => $summary,
                    'rows' => $rows,
                ])
                ->setPaper('a4', 'landscape')
                ->download("laba-rugi-harian-{$tgl}.pdf");
        }

        $title = 'Laporan Laba Rugi (Harian)';

        return view('laporan.laba_rugi', [
            'mode' => 'harian',
            'tgl' => $tgl,
            'summary' => $summary,
            'rows' => $rows,
            'title' => $title,
        ]);
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m')); // YYYY-MM
        $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        [$summary, $rows] = $this->buildReport($start, $end, 'month');

        if ($request->boolean('pdf')) {
            return Pdf::loadView('laporan.laba_rugi_pdf', [
                    'title' => 'Laporan Laba Rugi (Bulanan)',
                    'periodeText' => Carbon::createFromFormat('Y-m', $bulan)->format('M Y'),
                    'summary' => $summary,
                    'rows' => $rows,
                ])
                ->setPaper('a4', 'landscape')
                ->download("laba-rugi-bulanan-{$bulan}.pdf");
        }

        $title = 'Laporan Laba Rugi (Bulanan)';

        return view('laporan.laba_rugi', [
            'mode' => 'bulanan',
            'bulan' => $bulan,
            'summary' => $summary,
            'rows' => $rows,
            'title' => $title,
        ]);
    }

    public function tahunan(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $start = Carbon::create($tahun, 1, 1)->startOfYear();
        $end   = Carbon::create($tahun, 12, 31)->endOfYear();

        [$summary, $rows] = $this->buildReport($start, $end, 'year');

        if ($request->boolean('pdf')) {
            return Pdf::loadView('laporan.laba_rugi_pdf', [
                    'title' => 'Laporan Laba Rugi (Tahunan)',
                    'periodeText' => (string)$tahun,
                    'summary' => $summary,
                    'rows' => $rows,
                ])
                ->setPaper('a4', 'landscape')
                ->download("laba-rugi-tahunan-{$tahun}.pdf");
        }

        $title = 'Laporan Laba Rugi (Tahunan)';

        return view('laporan.laba_rugi', [
            'mode' => 'tahunan',
            'tahun' => $tahun,
            'summary' => $summary,
            'rows' => $rows,
            'title' => $title,
        ]);
    }

    private function buildReport(Carbon $start, Carbon $end, string $group): array
    {
        // agregasi detail per transaksi agar header (subtotal/voucher/tax) tidak dobel
        $detailAgg = DB::table('transaction_details')
            ->selectRaw('transaction_id')
            ->selectRaw('SUM(quantity * price_buy) as cogs')
            ->selectRaw('SUM(quantity * price) as gross_sales')
            ->selectRaw('SUM(discount) as item_discount')
            ->selectRaw('SUM(line_total) as net_item_total')
            ->groupBy('transaction_id');

        $base = DB::table('transactions as t')
            ->joinSub($detailAgg, 'd', fn($j) => $j->on('d.transaction_id', '=', 't.id'))
            ->whereBetween('t.transaction_date', [$start, $end])
            ->where('t.status', 'PAID'); // sesuaikan jika statusmu beda

        $summary = (clone $base)->selectRaw('
            COALESCE(SUM(t.subtotal - COALESCE(t.potongan_voucher,0)),0) as net_sales_ex_tax,
            COALESCE(SUM(d.cogs),0) as cogs,
            COALESCE(SUM((t.subtotal - COALESCE(t.potongan_voucher,0)) - d.cogs),0) as gross_profit,
            COALESCE(SUM(COALESCE(t.potongan_voucher,0)),0) as voucher_total,
            COALESCE(SUM(COALESCE(t.tax_amount,0)),0) as tax_total,
            COALESCE(SUM(d.gross_sales),0) as gross_sales,
            COALESCE(SUM(d.item_discount),0) as item_discount
        ')->first();

        if ($group === 'day') {
            $period = "DATE(t.transaction_date)";
        } elseif ($group === 'month') {
            $period = "DATE_FORMAT(t.transaction_date, '%Y-%m')";
        } else {
            $period = "YEAR(t.transaction_date)";
        }

        $rows = (clone $base)
            ->selectRaw("$period as period")
            ->selectRaw('COALESCE(SUM(t.subtotal - COALESCE(t.potongan_voucher,0)),0) as net_sales_ex_tax')
            ->selectRaw('COALESCE(SUM(d.cogs),0) as cogs')
            ->selectRaw('COALESCE(SUM((t.subtotal - COALESCE(t.potongan_voucher,0)) - d.cogs),0) as gross_profit')
            ->selectRaw('COALESCE(SUM(COALESCE(t.potongan_voucher,0)),0) as voucher_total')
            ->selectRaw('COALESCE(SUM(COALESCE(t.tax_amount,0)),0) as tax_total')
            ->selectRaw('COALESCE(SUM(d.gross_sales),0) as gross_sales')
            ->selectRaw('COALESCE(SUM(d.item_discount),0) as item_discount')
            ->groupByRaw($period)
            ->orderBy('period')
            ->get();

        return [$summary, $rows];
    }
}