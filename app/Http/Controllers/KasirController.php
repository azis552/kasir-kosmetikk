<?php
namespace App\Http\Controllers;

use App\Helpers\FormatHelper;
use App\Models\DiskonProduk;
use App\Models\Products;
use App\Models\Stocklevel;
use App\Models\Stockmovement;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected int $idleMinutes = 60;

    public function index()
    {
        $this->cleanupIdleDraft();

        $title = 'Kasir - Transaksi Penjualan';
        return view('kasir.index', compact('title'));
    }
    private function clearDashboardCache(): void
    {
        Cache::forget('dashboard_admin_7_' . today()->format('Ymd'));
        Cache::forget('dashboard_admin_30_' . today()->format('Ymd'));
    }

    /**
     * AUTO CLEAN DRAFT IDLE
     */
    private function cleanupIdleDraft()
    {
        $expired = now()->subMinutes($this->idleMinutes);

        $drafts = Transaction::where('status', 'DRAFT')
            ->where('updated_at', '<', $expired)
            ->get();

        foreach ($drafts as $trx) {
            if ($trx->details()->count() > 0) {
                $trx->update(['status' => 'VOID']);
            } else {
                $trx->delete();
            }
        }
    }

    public function tambah_produk(Request $request)
    {
        DB::beginTransaction();
        try {


            $barcode = $request->input('barcode');
            $productId = Products::where('barcode', $barcode)->value('id');

            $userId = auth()->id();
            $qty = 1;
            $terminalId = (new FormatHelper)->getMacAddress();

            $transaction = Transaction::where('user_id', $userId)
                ->where('terminal_id', $terminalId)
                ->where('status', 'DRAFT')
                ->first();

            if (!$transaction) {
                $transaction = Transaction::create([
                    'user_id' => $userId,
                    'transaction_date' => now(),
                    'terminal_id' => $terminalId,
                    'status' => 'DRAFT',
                ]);
            } else {
                $transaction->update([
                    'transaction_date' => now(),
                ]);
            }

            $product = Products::find($productId);
            if (!$product) {
                return response()->json(['success' => false, 'error' => 'Produk tidak ditemukan'], 404);
            }

            if (!$product->stocklevel || $product->stocklevel->quantity < 1) {
                return response()->json(['success' => false, 'error' => 'Stok produk tidak mencukupi'], 400);
            }

            $detail = TransactionDetail::where('transaction_id', $transaction->id)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if ($detail) {
                if ($detail->quantity + $qty > $product->stocklevel->quantity) {
                    return response()->json(['success' => false, 'error' => 'Stok produk tidak mencukupi'], 400);
                }

                $detail->quantity += $qty;
                $detail->line_total = ($detail->price - $detail->discount) * $detail->quantity;
                $detail->save();
            } else {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'price_buy' => $product->price_buy,
                    'discount' => 0,
                    'line_total' => $qty * $product->price,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function tampil_keranjang(Request $request)
    {
        $userId = auth()->id();

        $mac = (new \App\Helpers\FormatHelper)->getMacAddress();

        $terminalId = $mac;

        $transaction = Transaction::where('user_id', $userId)
            ->where('status', 'DRAFT')
            ->where('terminal_id', $terminalId)
            ->whereDate('transaction_date', now()->toDateString()) // ⬅️ TAMBAH
            ->first();
        if (!$transaction) {
            return response()->json(['success' => false, 'items' => []]);
        }

        $vouchers = 0;

        if ($transaction->voucher > 0) {
            $vouchers = Voucher::where('id', $transaction->voucher)->first();
        }

        $details = TransactionDetail::where('transaction_id', $transaction->id)
            ->with(['product', 'stock_product', 'diskons' => fn($q) => $q->active()])

            ->get();

        // diskon yang sudah diterapkan ke item ini (untuk "checked")

        $items = $details->map(function ($detail) {
            $diskonList = $detail->diskons->map(function ($d) {
                return [
                    'id' => $d->id,
                    'percentage' => (int) $d->diskon_percentage,
                    'min_qty' => (int) $d->min_qty,
                ];
            });

            // Pilih hanya satu diskon yang diterapkan berdasarkan diskon yang valid
            $appliedIds = [];
            if ($detail->discount > 0) {
                // Ambil diskon yang pertama kali valid berdasarkan kondisi
                foreach ($detail->diskons as $d) {
                    if ($detail->diskon_id == $d->id) {
                        $appliedIds[] = (int) $d->id;
                        break; // Hanya pilih satu diskon yang pertama valid
                    }
                }
            }

            return [
                'transaction_id' => $detail->transaction_id,
                'product_id' => $detail->product_id,
                'product_name' => $detail->product->name,
                'sku' => $detail->product->sku,
                'barcode' => $detail->product->barcode,
                'stock' => $detail->stock_product->quantity ?? 0,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'discount' => $diskonList,
                'applied_discount_ids' => $appliedIds,
                'line_total' => $detail->line_total,
            ];
        });

        return response()->json(['items' => $items, 'vouchers' => $vouchers, 'transactionId' => $transaction->id]);
    }

    public function updateDiskon(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|integer',
            'productId' => 'required|integer',
            'discountId' => 'required|integer',
            'action' => 'required|in:attach,detach',
        ]);
        $detail = TransactionDetail::where('transaction_id', $request->transactionId)
            ->where('product_id', $request->productId)
            ->firstOrFail();
        $diskon = DiskonProduk::findOrFail($request->discountId);
        if ($request->action === 'attach') {
            $detail->diskon_id = $diskon->id;
            $detail->discount = ($detail->price * ($diskon->diskon_percentage / 100)) * $detail->quantity;
            $detail->line_total = ($detail->price - (($diskon->diskon_percentage / 100) * $detail->price)) * $detail->quantity;
            $detail->save();

        } else {
            $detail->discount = 0;
            $detail->line_total = $detail->price * $detail->quantity;
            $detail->save();
        }

        return response()->json(['success' => true]);
    }

    public function hapus_produk(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|integer',
            'productId' => 'required|integer',
        ]);
        $detail = TransactionDetail::where('transaction_id', $request->transactionId)
            ->where('product_id', $request->productId)
            ->firstOrFail();
        $detail->delete();

        return response()->json(['success' => true]);
    }

    public function qty_produk(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|integer',
            'productId' => 'required|integer',
        ]);

        $detail = TransactionDetail::where('transaction_id', $request->transactionId)
            ->where('product_id', $request->productId)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | MODE 1: Update langsung dari input (Enter)
        |--------------------------------------------------------------------------
        */
        if ($request->has('qty')) {

            $newQty = (int) $request->qty;

            if ($newQty < 1) {
                return response()->json(['success' => false, 'error' => 'Quantity minimal 1'], 400);
            }

            if ($newQty > $detail->stock_product->quantity) {
                return response()->json(['success' => false, 'error' => 'Stok produk tidak mencukupi'], 400);
            }

            $detail->quantity = $newQty;

            /*
            |--------------------------------------------------------------------------
            | MODE 2: Tombol + / -
            |--------------------------------------------------------------------------
            */
        } elseif ($request->has('increase')) {

            $increase = filter_var($request->increase, FILTER_VALIDATE_BOOLEAN);

            if ($increase) {
                if ($detail->quantity + 1 > $detail->stock_product->quantity) {
                    return response()->json(['success' => false, 'error' => 'Stok produk tidak mencukupi'], 400);
                }
                $detail->quantity += 1;
            } else {
                if ($detail->quantity <= 1) {
                    return response()->json(['success' => false, 'error' => 'Quantity minimal 1'], 400);
                }
                $detail->quantity -= 1;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FIX: Hitung Ulang Line Total
        | Sebelumnya: DiskonProduk::find($detail->discount) → salah, discount = nominal rupiah
        | Sesudah:    DiskonProduk::find($detail->diskon_id) → benar, diskon_id = ID diskon
        |--------------------------------------------------------------------------
        */
        if ($detail->diskon_id) {
            $diskon = DiskonProduk::find($detail->diskon_id);
            if ($diskon) {
                $detail->line_total = ($detail->price - (($diskon->diskon_percentage / 100) * $detail->price))
                    * $detail->quantity;
                // update nominal discount juga sesuai qty baru
                $detail->discount = ($detail->price * ($diskon->diskon_percentage / 100)) * $detail->quantity;
            } else {
                // diskon sudah dihapus, reset
                $detail->discount = 0;
                $detail->diskon_id = null;
                $detail->line_total = $detail->price * $detail->quantity;
            }
        } else {
            $detail->line_total = $detail->price * $detail->quantity;
        }

        $detail->save();

        return response()->json(['success' => true]);
    }

    public function voucher(Request $request)
    {
        $userId = auth()->id();

        $transaction = Transaction::where('user_id', $userId)
            ->where('status', 'DRAFT')
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Tidak ada transaksi aktif']);
        }

        $voucherId = $request->input('voucher');
        if (empty($voucherId)) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak boleh kosong']);
        }

        // ── FIX: cari voucher dengan semua validasi sekaligus ──
        $voucher = Voucher::where('code', $voucherId)->first();

        // 1. Voucher tidak ditemukan
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak ditemukan']);
        }

        // 2. Voucher tidak aktif
        if (!$voucher->is_active) {
            return response()->json(['success' => false, 'message' => 'Voucher tidak aktif']);
        }

        // 3. Belum mulai berlaku
        if (now()->toDateString() < $voucher->start_date) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher belum berlaku. Berlaku mulai ' .
                    \Carbon\Carbon::parse($voucher->start_date)->format('d M Y'),
            ]);
        }

        // 4. Sudah kadaluarsa
        if (now()->toDateString() > $voucher->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah kadaluarsa sejak ' .
                    \Carbon\Carbon::parse($voucher->end_date)->format('d M Y'),
            ]);
        }

        // 5. Kuota habis
        if ($voucher->max_uses > 0 && $voucher->uses >= $voucher->max_uses) {
            return response()->json(['success' => false, 'message' => 'Voucher sudah tidak tersedia, kuota habis']);
        }

        // ── Semua validasi lolos, terapkan voucher ──
        $transaction->voucher = $voucher->id;
        $transaction->potongan_voucher = $voucher->discount_amount;
        $transaction->save();

        $voucher->uses += 1;
        $voucher->save();

        return response()->json(['success' => true]);
    }

    public function removeVoucher(Request $request)
    {
        $transactionId = $request->input('transactionId');
        $transaction = Transaction::where('id', $transactionId)->first();

        // Ensure the transaction exists
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found']);
        }

        // Check if the transaction has a voucher assigned
        if ($transaction->voucher) {
            // Find the voucher
            $voucher = Voucher::where('id', $transaction->voucher)->first();

            // Ensure the voucher exists
            if ($voucher) {
                // Decrease the voucher usage count
                $voucher->uses -= 1;
                $voucher->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Voucher not found']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'No voucher assigned to this transaction']);
        }

        // Remove the voucher and set the discount to 0
        $transaction->voucher = null;
        $transaction->potongan_voucher = 0;
        $transaction->save();

        return response()->json(['success' => true]);
    }

    public function bayar(Request $request)
    {
        $transactionId = $request->input('transactionId');
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
        }



        // Generate transaction code
        $transactionCode = "TRX-" . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);

        // Get transaction details, including products, stock, and active discounts
        $details = TransactionDetail::where('transaction_id', $transaction->id)
            ->with(['product', 'stock_product', 'diskons' => fn($q) => $q->active()])
            ->get();

        // Update transaction fields
        $transaction->transaction_code = $transactionCode;
        $transaction->transaction_date = now();
        $transaction->subtotal = $request->input('subTotal');
        $transaction->diskon_item = $request->input('discItem');
        $transaction->total = $request->input('grandTotal');
        $transaction->dibayar = $request->input('paid');
        $transaction->kembalian = $request->input('kembalian');
        $transaction->payment_method = $request->input('paymentMethod');
        $transaction->pelanggan_name = $request->input('pelanggan');
        $transaction->paid_at = now();
        $transaction->status = "PAID";
        $transaction->tax = $request->input('tax_rate');
        $transaction->tax_amount = $request->input('tax_amount');
        $transaction->save(); // Save the updated transaction

        // Loop through the transaction details to update stock levels and create stock movements
        $details->each(function ($detail) use ($transactionCode) {
            // Check if stock exists for the product
            $stoklevel = Stocklevel::where('product_id', $detail->product_id)->first();

            if ($stoklevel) {
                // Create stock movement record
                Stockmovement::create([
                    'product_id' => $detail->product_id,
                    'change_amount' => $detail->quantity,
                    'movement_type' => 'OUT', // OUT indicates stock is being sold
                    'description' => 'Penjualan kode transaksi ' . $transactionCode,
                    'ref_nota' => $transactionCode,
                ]);

                // Decrease the stock level based on the sold quantity
                $stoklevel->quantity -= $detail->quantity;
                $stoklevel->save(); // Save the updated stock level
            } else {
                // If no stock level found, log an error or handle it accordingly
                return response()->json(['success' => false, 'error' => 'Stock level not found for product ' . $detail->product_id]);
            }
        });
        // Generate the receipt content
        $receiptContent = $this->generatePosReceipt($transaction, $details);
        $this->clearDashboardCache();
        // Return the receipt content as a JSON response
        return response()->json([
            'success' => true,
            'receiptContent' => $receiptContent, // Send the raw receipt content for printing
        ]);
    }

    public function cetak($id)
    {


        $transaction = Transaction::find($id);
        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $details = TransactionDetail::where('transaction_id', $transaction->id)
            ->with(['product', 'stock_product', 'diskons'])
            ->get();

        return view('kasir.receipt', compact('transaction', 'details'));
    }


    // Method to generate the receipt content (plain text or ESC/POS format)
    private function generatePosReceipt($transaction, $details)
    {
        $line = str_repeat('-', 32) . "\n";

        $receipt = '';
        $receipt .= str_pad('STRUK PEMBELIAN', 32, ' ', STR_PAD_BOTH) . "\n";
        $receipt .= $line;

        // HEADER (DIPAKSA LURUS)
        $receipt .= str_pad('No. Transaksi', 16) . ": " . substr($transaction->transaction_code, 0, 14) . "\n";
        $receipt .= str_pad('Pelanggan', 16) . ": " . substr($transaction->pelanggan_name ?: '-', 0, 14) . "\n";
        $receipt .= $line;

        foreach ($details as $detail) {
            $product = $detail->product;

            $receipt .= str_pad('Produk', 16) . ": " . substr($product->name, 0, 14) . "\n";
            $receipt .= str_pad('Qty', 16) . ": " . $detail->quantity . "\n";
            $receipt .= str_pad('Harga', 16) . ": Rp " . number_format($detail->price, 0, ',', '.') . "\n";

            if ($detail->diskon_id) {
                $diskon = DiskonProduk::find($detail->diskon_id);
                if ($diskon) {
                    $receipt .= str_pad('Diskon', 16) . ": " . $diskon->diskon_percentage . "%\n";
                }
            }

            $receipt .= str_pad('Subtotal', 16) . ": Rp " . number_format($detail->line_total, 0, ',', '.') . "\n";
            $receipt .= $line;
        }

        // TOTALAN (KANAN RAPI)
        $receipt .= str_pad('Subtotal', 18) . "Rp " . number_format($transaction->subtotal, 0, ',', '.') . "\n";
        $receipt .= str_pad('Diskon', 18) . "Rp " . number_format($transaction->diskon_item, 0, ',', '.') . "\n";
        $receipt .= str_pad('Voucher', 18) . "Rp " . number_format($transaction->potongan_voucher, 0, ',', '.') . "\n";
        $receipt .= $line;

        $receipt .= str_pad('PPN ' . $transaction->tax . '%', 18)
            . "Rp " . number_format($transaction->tax_amount, 0, ',', '.') . "\n";

        $receipt .= str_pad('TOTAL BAYAR', 18)
            . "Rp " . number_format($transaction->total, 0, ',', '.') . "\n";

        $receipt .= $line;

        $receipt .= str_pad('Dibayar', 18)
            . "Rp " . number_format($transaction->dibayar, 0, ',', '.') . "\n";

        $receipt .= str_pad('Kembalian', 18)
            . "Rp " . number_format($transaction->kembalian, 0, ',', '.') . "\n";

        $receipt .= str_pad('Metode Bayar', 16) . ": " . strtoupper($transaction->payment_method) . "\n";
        $receipt .= $line;

        $receipt .= str_pad('Terima kasih atas kunjungan Anda', 32, ' ', STR_PAD_BOTH) . "\n";
        $receipt .= $line;

        return $receipt;
    }





    public function batal(Request $request)
    {
        $transactionId = $request->input('transactionId');

        $transaction = Transaction::where('id', $transactionId)
            ->where('status', 'DRAFT')
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
        }

        // FIX: kembalikan uses voucher jika transaksi DRAFT punya voucher
        if ($transaction->voucher) {
            $voucher = Voucher::find($transaction->voucher);
            if ($voucher && $voucher->uses > 0) {
                $voucher->uses -= 1;
                $voucher->save();
            }
            // Lepas voucher dari transaksi
            $transaction->voucher = null;
            $transaction->potongan_voucher = 0;
            $transaction->save();
        }

        // Hapus semua detail transaksi
        TransactionDetail::where('transaction_id', $transactionId)->delete();

        return response()->json(['success' => true]);
    }

    public function riwayat(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $transactions = Transaction::where('transaction_code', 'like', '%' . $search . '%')
                ->orderBy('created_at', 'desc')->paginate(10)->appends(request()->all());
        } else {
            $transactions = Transaction::where('transaction_code', '!=', '')->orderBy('created_at', 'desc')->paginate(10);
        }
        $title = "Riwayat Transaksi";
        return view('kasir.riwayat', compact('transactions', 'title'));

    }

    public function show($id)
    {

        $transaction = Transaction::find($id);
        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $details = TransactionDetail::where('transaction_id', $transaction->id)
            ->with(['product', 'stock_product', 'diskons'])
            ->get();
        $title = "Riwayat Transaksi";

        return view('kasir.detail_transaksi', compact('transaction', 'details', 'title'));
    }

    public function batalTransaksiSudahBayar(Request $request)
    {
        DB::beginTransaction();

        try {

            $transaction = Transaction::with('details')
                ->where('id', $request->transactionId)
                ->where('status', 'PAID')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan atau belum dibayar'
                ], 404);
            }

            $transactionCode = $transaction->transaction_code;

            foreach ($transaction->details as $detail) {

                // 1️⃣ Kembalikan stok
                $stock = Stocklevel::where('product_id', $detail->product_id)->first();

                if ($stock) {
                    $stock->quantity += $detail->quantity;
                    $stock->save();

                    // 2️⃣ Catat stock movement IN
                    Stockmovement::create([
                        'product_id' => $detail->product_id,
                        'change_amount' => $detail->quantity,
                        'movement_type' => 'in',
                        'description' => 'Pembatalan transaksi ' . $transactionCode,
                        'ref_nota' => $transactionCode,
                    ]);
                }
            }

            // 3️⃣ Kembalikan voucher
            if ($transaction->voucher) {
                $voucher = Voucher::find($transaction->voucher);

                if ($voucher && $voucher->uses > 0) {
                    $voucher->uses -= 1;
                    $voucher->save();
                }
            }

            // 4️⃣ Ubah status jadi VOID
            $transaction->status = 'VOID';
            $transaction->save();
            $this->clearDashboardCache();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
