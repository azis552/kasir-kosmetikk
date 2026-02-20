<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class RbacAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menghapus cache permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Definisikan permissions
        // =========================
// PERMISSIONS KASIR (sesuai route name)
// =========================
        $kasirPermissions = [
            // home
            'home',

            // dashboard kasir
            'dashboard.kasir',

            // kasir custom actions
            'kasir.tambahProduk',
            'kasir.keranjang',
            'kasir.updateDiskon',
            'kasir.hapusKeranjang',
            'kasir.qtyProduk',
            'kasir.voucher',
            'kasir.removeVoucher',
            'kasir.bayar',
            'kasir.cetak',
            'kasir.batal',

            // riwayat transaksi
            'transaksis.riwayat',
            'transaksis.show',

            // resource kasir (Route::resource('kasir', ...))
            'kasir.index',
            'kasir.create',
            'kasir.store',
            'kasir.show',
            'kasir.edit',
            'kasir.update',
            'kasir.destroy',
        ];


        // =========================
// PERMISSIONS ADMIN (sesuai route name)
// =========================
        $adminPermissions = [
            // home
            'home',

            // dashboard admin
            'dashboard.admin',

            // users (manual routes)
            'users.index',
            'users.create',
            'users.store',
            'users.edit',
            'users.update',
            'users.destroy',

            // roles (resource)
            'roles.index',
            'roles.create',
            'roles.store',
            'roles.show',
            'roles.edit',
            'roles.update',
            'roles.destroy',

            // product_categories (resource)
            'product_categories.index',
            'product_categories.create',
            'product_categories.store',
            'product_categories.show',
            'product_categories.edit',
            'product_categories.update',
            'product_categories.destroy',

            // products custom
            'products.storeDiskon',
            'products.diskon',     
            'products.updateStock',
            'products.diskon',
            'products.stock',

            // products (resource)
            'products.index',
            'products.create',
            'products.store',
            'products.show',
            'products.edit',
            'products.update',
            'products.destroy',

            // diskon (resource)
            'diskon.index',
            'diskon.create',
            'diskon.store',
            'diskon.show',
            'diskon.edit',
            'diskon.update',
            'diskon.destroy',

            // vouchers (resource)
            'vouchers.index',
            'vouchers.create',
            'vouchers.store',
            'vouchers.show',
            'vouchers.edit',
            'vouchers.update',
            'vouchers.destroy',

            // taxes (resource)
            'taxes.index',
            'taxes.create',
            'taxes.store',
            'taxes.show',
            'taxes.edit',
            'taxes.update',
            'taxes.destroy',

            // laporan
            'laporan.laporan_harian',
            'laporan.laporan_bulanan',
            'laporan.laporan_tahunan',

            // laporan laba rugi
            'laporan.laba_rugi_harian',
            'laporan.laba_rugi_bulanan',
            'laporan.laba_rugi_tahunan',

            // settings toko
            'settings.toko',
            'settings.toko.update',
            // theme
            'admin.theme',
            'admin.theme.update',
        ];


        // =========================
// (Opsional) kalau admin juga boleh akses menu kasir
// =========================
        $adminPermissions = array_values(array_unique(array_merge($adminPermissions, $kasirPermissions)));


        // =========================
// ALL PERMISSIONS (opsional)
// =========================
        $permissions = array_values(array_unique(array_merge($adminPermissions, $kasirPermissions)));



        // Membuat permissions jika belum ada
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Membuat role admin dan user jika belum ada
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);

        // Menyinkronkan permissions dengan role admin
        $adminRole->syncPermissions($adminPermissions);

        // Role user biasa, bisa diberi permission tertentu jika diinginkan
        $userRole->syncPermissions($kasirPermissions);

        // Membuat user default (admin)
        $admin = User::firstOrCreate([
            'name' => 'admin',
            'email' => 'admin@example.com',
        ], [
            'password' => Hash::make('password123'),  // Pastikan mengganti dengan password yang lebih aman
        ]);

        // Menugaskan role 'admin' ke user default
        $admin->assignRole('admin');

        // Menambahkan user biasa jika diperlukan
        $user = User::firstOrCreate([
            'name' => 'kasir',
            'email' => 'user@example.com',
        ], [
            'password' => Hash::make('password123'), // Ganti dengan password yang lebih aman
        ]);

        // Menugaskan role 'user' ke user biasa
        $user->assignRole('kasir');

        // Menghapus cache permissions setelah selesai
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
