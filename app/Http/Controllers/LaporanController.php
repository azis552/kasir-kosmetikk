<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // =====================================================
    // FIX N+1: Gunakan raw DB query agregasi, bukan
    // Collection::map() yang load setiap relasi satu-satu.
    // Untuk laporan dengan banyak transaksi (ratusan/ribuan),
    // ini jauh lebih efisien.
    // =====================================================

    private function queryTransaksi($start, $end, string $mode): array
    {
        // Agregasi per transaksi — 1 query saja
        $rows = DB::table('transactions as t')
            ->leftJoin('transaction_details as td', 'td.transaction_id', '=', 't.id')
            ->leftJoin('products as p', 'p.id', '=', 'td.product_id')
            ->where('t.status', 'PAID')
            ->whereBetween('t.transaction_date', [$start, $end])
            ->groupBy(
                't.id', 't.transaction_code', 't.transaction_date',
                't.potongan_voucher', 't.tax_amount'
            )
            ->select(
                't.id',
                't.transaction_code',
                't.transaction_date',
                't.potongan_voucher as voucher',
                't.tax_amount as pajak',
            )
            ->selectRaw('SUM(td.quantity) as totalQty')
            ->selectRaw('SUM(td.price * td.quantity) as omzetKotor')
            ->selectRaw('SUM(COALESCE(td.discount, 0)) as diskonProduk')
            ->selectRaw('SUM(td.price_buy * td.quantity) as totalHPP')
            ->orderBy('t.transaction_date')
            ->get();

        $reportData = $rows->map(function ($trx) use ($mode) {
            $omzetBersih = $trx->omzetKotor - $trx->diskonProduk - $trx->voucher;
            $keuntungan  = $omzetBersih - $trx->totalHPP;

            return [
                'transaction_code' => $trx->transaction_code,
                'waktu'            => $mode === 'harian'
                    ? date('H:i', strtotime($trx->transaction_date))
                    : $trx->transaction_date,
                'details'          => collect(),
                'totalQty'         => (int) $trx->totalQty,
                'omzetKotor'       => (float) $trx->omzetKotor,
                'diskonProduk'     => (float) $trx->diskonProduk,
                'voucher'          => (float) $trx->voucher,
                'pajak'            => (float) $trx->pajak,
                'omzetBersih'      => $omzetBersih,
                'totalHPP'         => (float) $trx->totalHPP, // ✅ fix: tambahkan ini
                'keuntungan'       => $keuntungan,
            ];
        });

        $totalAll = [
            'totalQty'    => $reportData->sum('totalQty'),
            'omzetKotor'  => $reportData->sum('omzetKotor'),
            'diskonProduk'=> $reportData->sum('diskonProduk'),
            'voucher'     => $reportData->sum('voucher'),
            'pajak'       => $reportData->sum('pajak'),
            'omzetBersih' => $reportData->sum('omzetBersih'),
            'totalHPP'    => $reportData->sum('totalHPP'),
            'keuntungan'  => $reportData->sum('keuntungan'),
        ];

        return [$reportData, $totalAll];
    }

    // =====================================================
    // Laporan Harian
    // =====================================================
    public function laporan_harian(Request $request)
    {
        $tgl   = $request->input('tgl', now()->format('Y-m-d'));
        $title = 'Laporan Harian';

        $start = Carbon::parse($tgl)->startOfDay();
        $end   = Carbon::parse($tgl)->endOfDay();

        [$reportData, $totalAll] = $this->queryTransaksi($start, $end, 'harian');

        if ($request->boolean('pdf')) {
            return Pdf::loadView('laporan.laporan', compact('reportData', 'tgl', 'totalAll'))
                ->setPaper('folio', 'landscape')
                ->download("laporan_harian_{$tgl}.pdf");
        }

        return view('laporan.laporan_harian', compact('title', 'reportData', 'tgl', 'totalAll'));
    }

    // =====================================================
    // Laporan Bulanan
    // =====================================================
    public function laporan_bulanan(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        $title = 'Laporan Bulanan';

        [$reportData, $totalAll] = $this->queryTransaksi($start, $end, 'bulanan');

        if ($request->boolean('pdf')) {
            return Pdf::loadView('laporan.laporan', compact('reportData', 'bulan', 'totalAll'))
                ->setPaper('folio', 'landscape')
                ->download("laporan_bulanan_{$bulan}.pdf");
        }

        return view('laporan.laporan_bulanan', compact('title', 'reportData', 'bulan', 'totalAll'));
    }

    // =====================================================
    // Laporan Tahunan
    // =====================================================
    public function laporan_tahunan(Request $request)
    {
        $tahun = $request->input('tahun', now()->format('Y'));
        $start = Carbon::createFromFormat('Y', $tahun)->startOfYear();
        $end   = Carbon::createFromFormat('Y', $tahun)->endOfYear();
        $title = 'Laporan Tahunan';

        [$reportData, $totalAll] = $this->queryTransaksi($start, $end, 'tahunan');

        if ($request->boolean('pdf')) {
            return Pdf::loadView('laporan.laporan', compact('reportData', 'tahun', 'totalAll'))
                ->setPaper('folio', 'landscape')
                ->download("laporan_tahunan_{$tahun}.pdf");
        }

        return view('laporan.laporan_tahunan', compact('title', 'reportData', 'tahun', 'totalAll'));
    }
}