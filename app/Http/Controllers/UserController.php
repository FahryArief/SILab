<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    // Tambah Pengguna Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,teknisi,kepala_lab,ka_prodi,peminjam',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    // Update Data Pengguna (Nama, Email, Role, Password Opsional)
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,teknisi,kepala_lab,ka_prodi,peminjam',
            'password' => 'nullable|string|min:6',
        ]);

        // Cegah mengubah role diri sendiri menjadi bukan super_admin
        if ($user->id === auth()->id() && $request->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:super_admin,teknisi,kepala_lab,ka_prodi,peminjam'
        ]);

        // Mencegah admin menghapus/mengubah role dirinya sendiri agar tidak terkunci dari sistem
        if ($user->id === auth()->id() && $request->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
        }

        $user->update([
            'role' => $request->role
        ]);

        return redirect()->back()->with('success', 'Role pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
    }
}
