<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    // Path AJAX penting yang tetap dicatat meski AJAX
    private array $importantAjax = [
        'kasir/bayar',
        'kasir/batal-paid',
        'kasir/batal',
    ];

    // Path yang di-skip (tidak dicatat)
    private array $skipPaths = [
        'kasir/keranjang',       // polling AJAX
        'kasir/qtyProduk',       // AJAX
        'kasir/updateDiskon',    // AJAX
        'kasih/tambah-produk',   // AJAX
        'kasir/hapusProduk',     // AJAX
        'kasir/removeVoucher',   // AJAX
        'kasir/voucher',         // AJAX
        'activity-logs',         // halaman log itu sendiri
        'up',                    // health check
    ];

    // Map path → deskripsi yang mudah dibaca
    private array $actionMap = [
        'GET kasir'                     => '🛒 Buka halaman kasir',
        // bayar & batal ditangani di buildSmartDescription agar bisa ambil kode transaksi
        'POST kasir/batal'              => '❌ Batalkan keranjang',
        'GET transaksis/riwayat'        => '📋 Lihat riwayat transaksi',
        'GET products'                  => '📦 Lihat daftar produk',
        'GET products/create'           => '➕ Buka form tambah produk',
        'POST products'                 => '➕ Tambah produk baru',
        'GET products/stock-alert'      => '⚠️ Lihat stock alert',
        'GET product_categories'        => '🗂️ Lihat kategori produk',
        'POST product_categories'       => '➕ Tambah kategori',
        'GET users'                     => '👥 Lihat daftar user',
        'GET users/create'              => '➕ Buka form tambah user',
        'POST users'                    => '➕ Tambah user baru',
        'GET roles'                     => '🔑 Lihat daftar role',
        'GET laporan/laporan_harian'    => '📊 Lihat laporan harian',
        'GET laporan/laporan_bulanan'   => '📊 Lihat laporan bulanan',
        'GET laporan/laporan_tahunan'   => '📊 Lihat laporan tahunan',
        'GET laporan/laba-rugi/harian'  => '📈 Lihat laba rugi harian',
        'GET laporan/laba-rugi/bulanan' => '📈 Lihat laba rugi bulanan',
        'GET laporan/laba-rugi/tahunan' => '📈 Lihat laba rugi tahunan',
        'GET settings/toko'             => '⚙️ Lihat pengaturan toko',
        'PUT settings/toko'             => '⚙️ Update pengaturan toko',
        'GET dashboard/admin'           => '🏠 Buka dashboard admin',
        'GET dashboard/kasir'           => '🏠 Buka dashboard kasir',
        'POST login'                    => '🔐 Login',
        'POST logout'                   => '🚪 Logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (!auth()->check()) return;

        $path   = $request->path();
        $method = $request->method();

        // Skip path tertentu
        foreach ($this->skipPaths as $skip) {
            if (str_starts_with($path, $skip)) return;
        }

        // Cek apakah termasuk aksi AJAX penting
        $isImportantAjax = false;
        foreach ($this->importantAjax as $important) {
            if (str_starts_with($path, $important)) {
                $isImportantAjax = true;
                break;
            }
        }

        // Skip AJAX biasa, kecuali yang penting
        if (($request->ajax() || $request->wantsJson()) && !$isImportantAjax) return;

        // Buat deskripsi
        $actionKey   = "{$method} {$path}";
        $description = $this->actionMap[$actionKey]
            ?? $this->buildSmartDescription($method, $path, $request);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action'  => $actionKey,
            'details' => $description,
        ]);
    }

    private function buildSmartDescription(string $method, string $path, Request $request): string
    {
        $segments = explode('/', $path);

        // Kasir bayar — ambil kode transaksi dari DB
        if ($path === 'kasir/bayar' && $method === 'POST') {
            $trxId   = $request->input('transactionId');
            $trxCode = \App\Models\Transaction::where('id', $trxId)->value('transaction_code')
                ?? "ID:{$trxId}";
            return "💰 Transaksi pembayaran — {$trxCode}";
        }

        // Kasir batal-paid — ambil kode transaksi dari DB
        if ($path === 'kasir/batal-paid' && $method === 'POST') {
            $trxId   = $request->input('transactionId');
            $trxCode = \App\Models\Transaction::where('id', $trxId)->value('transaction_code')
                ?? "ID:{$trxId}";
            return "↩️ Batalkan transaksi sudah bayar — {$trxCode}";
        }

        if (count($segments) >= 3 && $segments[0] === 'products' && end($segments) === 'stock') {
            return "📦 Manage stok produk ID: {$segments[1]}";
        }

        if (count($segments) >= 3 && $segments[0] === 'products' && end($segments) === 'diskon') {
            return "🏷️ Kelola diskon produk ID: {$segments[1]}";
        }

        if ($method === 'DELETE' && $segments[0] === 'products') {
            return "🗑️ Hapus produk ID: {$segments[1]}";
        }

        if ($segments[0] === 'products' && isset($segments[1]) && is_numeric($segments[1])) {
            return $method === 'PUT'
                ? "✏️ Update produk ID: {$segments[1]}"
                : "👁️ Lihat produk ID: {$segments[1]}";
        }

        if ($method === 'DELETE' && $segments[0] === 'users') {
            return "🗑️ Hapus user ID: {$segments[1]}";
        }

        if ($segments[0] === 'users' && isset($segments[1]) && is_numeric($segments[1])) {
            return $method === 'PUT'
                ? "✏️ Update user ID: {$segments[1]}"
                : "👁️ Lihat user ID: {$segments[1]}";
        }

        if ($segments[0] === 'transaksis' && isset($segments[2]) && $segments[2] === 'detail') {
            return "🧾 Lihat detail transaksi ID: {$segments[1]}";
        }

        if ($segments[0] === 'vouchers') {
            return match($method) {
                'POST'   => '🎫 Tambah voucher baru',
                'PUT'    => "🎫 Update voucher ID: " . ($segments[1] ?? '-'),
                'DELETE' => "🗑️ Hapus voucher ID: " . ($segments[1] ?? '-'),
                default  => "🎫 Halaman voucher",
            };
        }

        if ($segments[0] === 'kasir' && isset($segments[1]) && $segments[1] === 'cetak') {
            return "🖨️ Cetak struk transaksi ID: " . ($segments[2] ?? '-');
        }

        return ucfirst($method) . ' ' . $path;
    }
}