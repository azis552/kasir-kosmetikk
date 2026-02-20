<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function laporan_harian(Request $request)
    {
        $tgl = $request->input('tgl', now()->format('Y-m-d'));
        $title = 'Laporan Harian';

        $transaksi = Transaction::with(['details.product'])
            ->whereDate('transaction_date', $tgl)
            ->get();

        $reportData = $transaksi->map(function ($trx) {

            $totalQty = $trx->details->sum('quantity');

            $omzetKotor = $trx->details->sum(function ($d) {
                return $d->price * $d->quantity;
            });

            $diskonProduk = $trx->details->sum('discount');

            $voucher = $trx->potongan_voucher ?? 0;

            $omzetBersih = $omzetKotor - $diskonProduk - $voucher;

            $totalHPP = $trx->details->sum(function ($d) {
                return $d->price_buy * $d->quantity;
            });

            $keuntungan = $omzetBersih - $totalHPP;

            return [
                'transaction_code' => $trx->transaction_code,
                'waktu' => date('H:i', strtotime($trx->transaction_date)),
                'details' => $trx->details,

                'totalQty' => $totalQty,
                'omzetKotor' => $omzetKotor,
                'diskonProduk' => $diskonProduk,
                'voucher' => $voucher,
                'pajak' => $trx->tax_amount,
                'omzetBersih' => $omzetBersih,
                'totalHPP' => $totalHPP,
                'keuntungan' => $keuntungan,
            ];
        });

        $totalAll = [
            'totalQty' => $reportData->sum('totalQty'),
            'omzetKotor' => $reportData->sum('omzetKotor'),
            'diskonProduk' => $reportData->sum('diskonProduk'),
            'voucher' => $reportData->sum('voucher'),
            'pajak' => $reportData->sum('pajak'),
            'omzetBersih' => $reportData->sum('omzetBersih'),
            'totalHPP' => $reportData->sum('totalHPP'),
            'keuntungan' => $reportData->sum('keuntungan'),
        ];

        if ($request->boolean('pdf')) {
            $pdf = PDF::loadView('laporan.laporan', compact('reportData', 'tgl', 'totalAll'))
                ->setPaper('folio', 'landscape');

            return $pdf->download("laporan_harian_$tgl.pdf");
        }

        return view('laporan.laporan_harian', compact('title', 'reportData', 'tgl', 'totalAll'));
    }


    public function laporan_bulanan(Request $request, $bulan = null)
    {
        $bulan = $request->input('bulan', now()->format('Y-m')); // YYYY-MM
        $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        $title = 'Laporan Bulanan';

        $transaksi = Transaction::with(['details.product'])
            ->whereBetween('transaction_date', [$start, $end])
            ->get();

        $reportData = $transaksi->map(function ($trx) {

            $totalQty = $trx->details->sum('quantity');

            $omzetKotor = $trx->details->sum(function ($d) {
                return $d->price * $d->quantity;
            });

            $diskonProduk = $trx->details->sum('discount');

            $voucher = $trx->potongan_voucher ?? 0;

            $omzetBersih = $omzetKotor - $diskonProduk - $voucher;

            $totalHPP = $trx->details->sum(function ($d) {
                return $d->price_buy * $d->quantity;
            });

            $keuntungan = $omzetBersih - $totalHPP;

            return [
                'transaction_code' => $trx->transaction_code,
                'waktu' => $trx->transaction_date,
                'details' => $trx->details,

                'totalQty' => $totalQty,
                'omzetKotor' => $omzetKotor,
                'diskonProduk' => $diskonProduk,
                'voucher' => $voucher,
                'pajak' => $trx->tax_amount,
                'omzetBersih' => $omzetBersih,
                'totalHPP' => $totalHPP,
                'keuntungan' => $keuntungan,
            ];
        });

        $totalAll = [
            'totalQty' => $reportData->sum('totalQty'),
            'omzetKotor' => $reportData->sum('omzetKotor'),
            'diskonProduk' => $reportData->sum('diskonProduk'),
            'voucher' => $reportData->sum('voucher'),
            'pajak' => $reportData->sum('pajak'),
            'omzetBersih' => $reportData->sum('omzetBersih'),
            'totalHPP' => $reportData->sum('totalHPP'),
            'keuntungan' => $reportData->sum('keuntungan'),
        ];

        if ($request->boolean('pdf')) {
            $pdf = PDF::loadView('laporan.laporan', compact('reportData', 'bulan', 'totalAll'))
                ->setPaper('folio', 'landscape');

            return $pdf->download("laporan_bulanan_$bulan.pdf");
        }

        return view('laporan.laporan_bulanan', compact('title', 'reportData', 'bulan', 'totalAll'));
    }

    public function laporan_tahunan(Request $request, $tahun = null)
    {
        $bulan = $request->input('tahun', now()->format('Y')); // YYYY-MM
        $start = Carbon::createFromFormat('Y', $bulan)->startOfYear();
        $end   = Carbon::createFromFormat('Y', $bulan)->endOfYear();
        $title = 'Laporan Tahunan';

        $transaksi = Transaction::with(['details.product'])
            ->whereBetween('transaction_date', [$start, $end])
            ->get();

        $reportData = $transaksi->map(function ($trx) {

            $totalQty = $trx->details->sum('quantity');

            $omzetKotor = $trx->details->sum(function ($d) {
                return $d->price * $d->quantity;
            });

            $diskonProduk = $trx->details->sum('discount');

            $voucher = $trx->potongan_voucher ?? 0;

            $omzetBersih = $omzetKotor - $diskonProduk - $voucher;

            $totalHPP = $trx->details->sum(function ($d) {
                return $d->price_buy * $d->quantity;
            });

            $keuntungan = $omzetBersih - $totalHPP;

            return [
                'transaction_code' => $trx->transaction_code,
                'waktu' => $trx->transaction_date,
                'details' => $trx->details,

                'totalQty' => $totalQty,
                'omzetKotor' => $omzetKotor,
                'diskonProduk' => $diskonProduk,
                'voucher' => $voucher,
                'pajak' => $trx->tax_amount,
                'omzetBersih' => $omzetBersih,
                'totalHPP' => $totalHPP,
                'keuntungan' => $keuntungan,
            ];
        });

        $totalAll = [
            'totalQty' => $reportData->sum('totalQty'),
            'omzetKotor' => $reportData->sum('omzetKotor'),
            'diskonProduk' => $reportData->sum('diskonProduk'),
            'voucher' => $reportData->sum('voucher'),
            'pajak' => $reportData->sum('pajak'),
            'omzetBersih' => $reportData->sum('omzetBersih'),
            'totalHPP' => $reportData->sum('totalHPP'),
            'keuntungan' => $reportData->sum('keuntungan'),
        ];

        if ($request->boolean('pdf')) {
            $pdf = PDF::loadView('laporan.laporan', compact('reportData', 'tahun', 'totalAll'))
                ->setPaper('folio', 'landscape');

            return $pdf->download("laporan_tahunan_$tahun.pdf");
        }

        return view('laporan.laporan_tahunan', compact('title', 'reportData', 'tahun', 'totalAll'));
    }


}
