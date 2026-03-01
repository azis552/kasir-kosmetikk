<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    // Menampilkan daftar pengguna
    public function index(Request $request)
    {
        // Ambil parameter pencarian dari request
        $search = $request->get('search');
        $sort = $request->get('sort', 'name'); // Default ke 'name' jika tidak ada
        $direction = $request->get('direction', 'asc'); // Default pengurutan adalah 'asc'

        // Query untuk mencari dan mengurutkan data
        $query = User::query();

        // Pencarian berdasarkan nama atau email
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // Pengurutan berdasarkan kolom dan arah
        $query->orderBy($sort, $direction);

        // Pagination (10 data per halaman)
        $users = $query->paginate(10)->appends(request()->all()); // Agar parameter pencarian tetap ada saat berpindah halaman

        // Kirim data ke tampilan
        $title = 'User Management';
        return view('users.index', compact('users', 'title'));
    }

    // Menampilkan form untuk membuat pengguna baru
    public function create()
    {
        $roles = Role::all();
        $title = 'User Management';
        return view('users.create', compact('roles', 'title'));
    }

    // Menyimpan pengguna baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:users,name'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles($data['roles']);


        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // Menampilkan form untuk mengedit pengguna
    public function edit(User $user)
    {
        $roles = Role::all();
        $title = 'User Management';
        return view('users.edit', compact('user', 'roles', 'title'));
    }

    // Menyimpan perubahan pengguna
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ? Hash::make($data['password']) : $user->password,
        ]);

        $user->syncRoles($data['roles']);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Menghapus pengguna
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
