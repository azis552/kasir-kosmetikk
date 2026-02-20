<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // Menampilkan daftar role
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan pengurutan
        $search = $request->get('search');
        $sort = $request->get('sort', 'name'); // Default kolom untuk sort adalah 'name'
        $direction = $request->get('direction', 'asc'); // Default pengurutan adalah 'asc'

        // Query untuk mencari dan mengurutkan data
        $query = Role::query();

        // Pencarian berdasarkan nama role
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Pengurutan berdasarkan kolom dan arah
        $query->orderBy($sort, $direction);

        // Pagination (10 data per halaman)
        $roles = $query->paginate(10)->appends(request()->all());
        $title = 'Role Management';

        // Kirim data ke tampilan
        return view('roles.index', compact('roles', 'title'));
    }

    // Menampilkan form untuk membuat role
    public function create()
    {
        $permissions = Permission::all();
        $title = 'Role Management';
        return view('roles.create', compact('permissions', 'title'));
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',  // pastikan permission valid
        ]);

        $role = Role::create(['name' => $request->name]);

        // Assign permissions ke role
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    // Menampilkan form untuk mengedit role
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $title = 'Role Management';
        return view('roles.edit', compact('role', 'permissions', 'title'));
    }

    // Menyimpan perubahan role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id . '|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);

        // Sync permissions
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    // Menghapus role
    public function destroy(Role $role)
    {
        // Jangan izinkan menghapus role yang sistemik (misalnya 'admin')
        if ($role->name == 'admin') {
            return redirect()->route('roles.index')->with('error', 'Cannot delete the admin role');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}
