<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanLabaRugiController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StoreSettingController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Commands\Show;

Route::get('/', function () {
    return view('auth.login');
});
Route::middleware(['auth', 'log.user.activity'])->group(function () {
    
    Route::get('kasir/cetak/{id}', [KasirController::class, 'cetak'])->name('kasir.cetak');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('kasih/tambah-produk', [KasirController::class, 'tambah_produk'])->name('kasir.tambahProduk');
    Route::get('kasir/keranjang', [KasirController::class, 'tampil_keranjang'])->name('kasir.keranjang');
    Route::post('kasir/updateDiskon', [KasirController::class, 'updateDiskon'])->name('kasir.updateDiskon');
    Route::post('kasir/hapusProduk', [KasirController::class, 'hapus_produk'])->name('kasir.hapusKeranjang');
    Route::post('kasir/qtyProduk', [KasirController::class, 'qty_produk'])->name('kasir.qtyProduk');
    Route::post('kasir/voucher', [KasirController::class, 'voucher'])->name('kasir.voucher');
    Route::post('kasir/removeVoucher', [KasirController::class, 'removeVoucher'])->name('kasir.removeVoucher');
    Route::post('kasir/bayar', [KasirController::class, 'bayar'])->name('kasir.bayar');
    Route::post('kasir/batal', [KasirController::class, 'batal'])->name('kasir.batal');
    Route::get('transaksis/riwayat', [KasirController::class, 'riwayat'])->name('transaksis.riwayat');
    Route::get('transaksis/{id}/detail', [KasirController::class, 'Show'])->name('transaksis.show');
    Route::resource('kasir', KasirController::class);
    Route::middleware(['role:kasir'])->group(function () {
        Route::get('/dashboard/kasir', [DashboardController::class, 'kasir'])->name('dashboard.kasir');
       
    });
Route::get('/users', [UserController::class, 'index'])->name('users.index');
    

});
Route::middleware(['auth', 'role:admin', 'log.user.activity'])->group(function () {
      
    
    Route::resource('products', ProductController::class);
    // Menampilkan daftar pengguna
   
    // Form untuk membuat pengguna baru
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    // Menyimpan pengguna baru
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    // Form untuk mengedit pengguna
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    // Menyimpan perubahan pengguna
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    // Menghapus pengguna
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::resource('roles', RoleController::class);
    Route::resource('product_categories', ProductCategoryController::class);
    Route::PUT('products/{product}/diskon', [ProductController::class, 'storeDiskon'])->name('products.storeDiskon');
    Route::get('products/{product}/diskon', [ProductController::class, 'diskon'])->name('products.diskon');
    Route::put('products/updateStock/{product}', [ProductController::class, 'updateStock'])->name('products.updateStock');
    Route::get('products/{product}/stock', [ProductController::class, 'stock'])->name('products.stock');
    Route::resource('diskon', DiskonController::class);
    Route::resource('vouchers', VoucherController::class);
    Route::resource('taxes', TaxController::class);
    Route::get('laporan/laporan_harian', [LaporanController::class, 'laporan_harian'])->name('laporan.laporan_harian');
    Route::get('laporan/laporan_bulanan', [LaporanController::class, 'laporan_bulanan'])->name('laporan.laporan_bulanan');
    Route::get('laporan/laporan_tahunan', [LaporanController::class, 'laporan_tahunan'])->name('laporan.laporan_tahunan');
    Route::get('/laporan/laba-rugi/harian', [LaporanLabaRugiController::class, 'harian'])
        ->name('laporan.laba_rugi_harian');

    Route::get('/laporan/laba-rugi/bulanan', [LaporanLabaRugiController::class, 'bulanan'])
        ->name('laporan.laba_rugi_bulanan');

    Route::get('/laporan/laba-rugi/tahunan', [LaporanLabaRugiController::class, 'tahunan'])
        ->name('laporan.laba_rugi_tahunan');
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    });

    Route::get('/settings/toko', [StoreSettingController::class, 'edit'])->name('settings.toko');
    Route::put('/settings/toko', [StoreSettingController::class, 'update'])->name('settings.toko.update');

    Route::get('/admin/theme', [ThemeController::class, 'index'])->name('admin.theme');
    Route::post('/admin/theme', [ThemeController::class, 'update'])->name('admin.theme.update');
    Route::post('/kasir/batal-paid', [KasirController::class, 'batalTransaksiSudahBayar'])->name('kasir.batal.paid');



});