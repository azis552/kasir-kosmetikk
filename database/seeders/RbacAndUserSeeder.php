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
     * =============================================================
     * CARA MENAMBAH PERMISSION BARU:
     *
     * 1. Tambahkan entry di $routePermissions di bawah
     * 2. Set role mana saja yang boleh akses: 'admin', 'kasir', atau keduanya
     * 3. Jalankan: php artisan db:seed --class=RbacAndUserSeeder
     *
     * Format:
     *   'nama.route' => ['admin', 'kasir'],   // admin & kasir boleh
     *   'nama.route' => ['admin'],             // hanya admin
     *   'nama.route' => ['kasir'],             // hanya kasir
     * =============================================================
     */
    private array $routePermissions = [

        // -------------------------------------------------------
        // SHARED — kasir & admin
        // -------------------------------------------------------
        'home'                  => ['admin', 'kasir'],
        'dashboard.kasir'       => ['admin', 'kasir'],

        // Kasir transaksi
        'kasir.index'           => ['admin', 'kasir'],
        'kasir.create'          => ['admin', 'kasir'],
        'kasir.store'           => ['admin', 'kasir'],
        'kasir.show'            => ['admin', 'kasir'],
        'kasir.edit'            => ['admin', 'kasir'],
        'kasir.update'          => ['admin', 'kasir'],
        'kasir.destroy'         => ['admin', 'kasir'],
        'kasir.tambahProduk'    => ['admin', 'kasir'],
        'kasir.keranjang'       => ['admin', 'kasir'],
        'kasir.updateDiskon'    => ['admin', 'kasir'],
        'kasir.hapusKeranjang'  => ['admin', 'kasir'],
        'kasir.qtyProduk'       => ['admin', 'kasir'],
        'kasir.voucher'         => ['admin', 'kasir'],
        'kasir.removeVoucher'   => ['admin', 'kasir'],
        'kasir.bayar'           => ['admin', 'kasir'],
        'kasir.cetak'           => ['admin', 'kasir'],
        'kasir.batal'           => ['admin', 'kasir'],
        'transaksis.riwayat'    => ['admin', 'kasir'],
        'transaksis.show'       => ['admin', 'kasir'],

        // -------------------------------------------------------
        // ADMIN ONLY
        // -------------------------------------------------------

        // Dashboard
        'dashboard.admin'           => ['admin'],

        // User management
        'users.index'               => ['admin'],
        'users.create'              => ['admin'],
        'users.store'               => ['admin'],
        'users.edit'                => ['admin'],
        'users.update'              => ['admin'],
        'users.destroy'             => ['admin'],

        // Role management
        'roles.index'               => ['admin'],
        'roles.create'              => ['admin'],
        'roles.store'               => ['admin'],
        'roles.show'                => ['admin'],
        'roles.edit'                => ['admin'],
        'roles.update'              => ['admin'],
        'roles.destroy'             => ['admin'],

        // Product categories
        'product_categories.index'  => ['admin'],
        'product_categories.create' => ['admin'],
        'product_categories.store'  => ['admin'],
        'product_categories.show'   => ['admin'],
        'product_categories.edit'   => ['admin'],
        'product_categories.update' => ['admin'],
        'product_categories.destroy'=> ['admin'],

        // Products
        'products.index'            => ['admin'],
        'products.create'           => ['admin'],
        'products.store'            => ['admin'],
        'products.show'             => ['admin'],
        'products.edit'             => ['admin'],
        'products.update'           => ['admin'],
        'products.destroy'          => ['admin'],
        'products.stock'            => ['admin'],
        'products.updateStock'      => ['admin'],
        'products.diskon'           => ['admin'],
        'products.storeDiskon'      => ['admin'],
        'products.stock-alert'      => ['admin'],

        // Diskon
        'diskon.index'              => ['admin'],
        'diskon.create'             => ['admin'],
        'diskon.store'              => ['admin'],
        'diskon.show'               => ['admin'],
        'diskon.edit'               => ['admin'],
        'diskon.update'             => ['admin'],
        'diskon.destroy'            => ['admin'],

        // Vouchers
        'vouchers.index'            => ['admin'],
        'vouchers.create'           => ['admin'],
        'vouchers.store'            => ['admin'],
        'vouchers.show'             => ['admin'],
        'vouchers.edit'             => ['admin'],
        'vouchers.update'           => ['admin'],
        'vouchers.destroy'          => ['admin'],

        // Taxes
        'taxes.index'               => ['admin'],
        'taxes.create'              => ['admin'],
        'taxes.store'               => ['admin'],
        'taxes.show'                => ['admin'],
        'taxes.edit'                => ['admin'],
        'taxes.update'              => ['admin'],
        'taxes.destroy'             => ['admin'],

        // Laporan
        'laporan.laporan_harian'    => ['admin'],
        'laporan.laporan_bulanan'   => ['admin'],
        'laporan.laporan_tahunan'   => ['admin'],
        'laporan.laba_rugi_harian'  => ['admin'],
        'laporan.laba_rugi_bulanan' => ['admin'],
        'laporan.laba_rugi_tahunan' => ['admin'],

        // Settings & theme
        'settings.toko'             => ['admin'],
        'settings.toko.update'      => ['admin'],
        'admin.theme'               => ['admin'],
        'admin.theme.update'        => ['admin'],

        // Batal transaksi sudah bayar
        'kasir.batal.paid'          => ['admin'],

        // -------------------------------------------------------
        // TAMBAHKAN PERMISSION BARU DI SINI
        // Contoh:
        // 'laporan.export'         => ['admin'],
        // 'produk.import'          => ['admin', 'kasir'],
        // -------------------------------------------------------
    ];

    public function run(): void
    {
        // Reset cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Pastikan role ada
        $roles = [
            'admin' => Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']),
            'kasir' => Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']),
        ];

        // Kumpulkan permission per role
        $permsByRole = ['admin' => [], 'kasir' => []];

        foreach ($this->routePermissions as $permName => $allowedRoles) {
            // Buat permission jika belum ada
            Permission::firstOrCreate([
                'name'       => $permName,
                'guard_name' => 'web',
            ]);

            foreach ($allowedRoles as $roleName) {
                if (isset($permsByRole[$roleName])) {
                    $permsByRole[$roleName][] = $permName;
                }
            }
        }

        // Sync permission ke masing-masing role
        foreach ($roles as $roleName => $roleModel) {
            $roleModel->syncPermissions($permsByRole[$roleName]);
        }

        // Buat user default jika belum ada
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'admin',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->syncRoles(['admin']);

        $kasir = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'kasir',
                'password' => Hash::make('password123'),
            ]
        );
        $kasir->syncRoles(['kasir']);

        // Reset cache lagi setelah selesai
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('✅ Roles & permissions berhasil disinkronkan.');
        $this->command->table(
            ['Role', 'Jumlah Permission'],
            collect($permsByRole)->map(fn($perms, $role) => [$role, count($perms)])->values()->toArray()
        );
    }
}