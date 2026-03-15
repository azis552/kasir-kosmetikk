<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * =============================================================
     * GRUP MODUL — untuk mengelompokkan permission di tampilan UI.
     *
     * Cara menambah route baru:
     * 1. Tambahkan permission di RbacAndUserSeeder.php
     * 2. Jika modul sudah ada, permission otomatis masuk grup.
     * 3. Jika modul baru, tambahkan key di $moduleGroups di bawah.
     * =============================================================
     */
    private array $moduleGroups = [
        'Dashboard'         => ['dashboard.admin', 'dashboard.kasir', 'home'],
        'Kasir & Transaksi' => ['kasir.*', 'transaksis.*'],
        'User Management'   => ['users.*'],
        'Role Management'   => ['roles.*'],
        'Produk'            => ['products.*'],
        'Kategori Produk'   => ['product_categories.*'],
        'Diskon'            => ['diskon.*'],
        'Voucher'           => ['vouchers.*'],
        'Pajak'             => ['taxes.*'],
        'Laporan'           => ['laporan.*'],
        'Pengaturan'        => ['settings.*', 'admin.theme*'],
    ];

    // Tampilkan daftar role
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $sort      = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $query = Role::withCount('permissions');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy($sort, $direction);
        $roles = $query->paginate(10)->appends(request()->all());
        $title = 'Role Management';

        return view('roles.index', compact('roles', 'title'));
    }

    // Form buat role baru
    public function create()
    {
        $groupedPermissions = $this->getGroupedPermissions();
        $title = 'Role Management';
        return view('roles.create', compact('groupedPermissions', 'title'));
    }

    // Simpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|unique:roles,name|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' berhasil dibuat.");
    }

    // Form edit role
    public function edit(Role $role)
    {
        $groupedPermissions    = $this->getGroupedPermissions();
        $rolePermissionNames   = $role->permissions->pluck('name')->toArray();
        $title = 'Role Management';
        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissionNames', 'title'));
    }

    // Simpan perubahan role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'          => 'required|string|unique:roles,name,' . $role->id . '|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' berhasil diperbarui.");
    }

    // Hapus role
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['admin', 'kasir'])) {
            return redirect()->route('roles.index')
                ->with('error', "Role '{$role->name}' tidak bisa dihapus.");
        }

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }

    // -------------------------------------------------------
    // Helper: Kelompokkan semua permission berdasarkan modul
    // -------------------------------------------------------
    private function getGroupedPermissions(): array
    {
        $allPermissions = Permission::orderBy('name')->get();
        $grouped        = [];
        $assigned       = [];

        foreach ($this->moduleGroups as $moduleName => $patterns) {
            $grouped[$moduleName] = [];

            foreach ($allPermissions as $perm) {
                foreach ($patterns as $pattern) {
                    // Dukung wildcard sederhana: 'kasir.*'
                    $regex = '/^' . str_replace(['.', '*'], ['\.', '.*'], $pattern) . '$/';
                    if (preg_match($regex, $perm->name) && !in_array($perm->name, $assigned)) {
                        $grouped[$moduleName][] = $perm;
                        $assigned[]             = $perm->name;
                        break;
                    }
                }
            }

            // Hapus modul kosong
            if (empty($grouped[$moduleName])) {
                unset($grouped[$moduleName]);
            }
        }

        // Permission yang belum masuk grup manapun → taruh di "Lainnya"
        $ungrouped = $allPermissions->filter(
            fn($p) => !in_array($p->name, $assigned)
        )->values();

        if ($ungrouped->count() > 0) {
            $grouped['Lainnya'] = $ungrouped->all();
        }

        return $grouped;
    }
}