<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use App\Support\Role;

class UserController extends Controller
{
    /**
     * Valid roles for the application.
     */
    public const VALID_ROLES = Role::ALL;

    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::select(['id', 'name', 'email', 'role', 'created_at'])
            ->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    // Tambah Pengguna Baru
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        Log::info('User created', [
            'admin_id' => auth()->id(),
            'created_user_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    // Update Data Pengguna (Nama, Email, Role, Password Opsional)
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        // Cegah mengubah role diri sendiri
        if ($user->id === auth()->id() && $request->role !== $user->role) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
        }

        return DB::transaction(function () use ($request, $user) {
            // Cegah demosi super_admin terakhir dengan lockForUpdate
            if ($user->role === 'super_admin' && $request->role !== 'super_admin') {
                $superAdminCount = User::where('role', 'super_admin')->lockForUpdate()->count();
                if ($superAdminCount <= 1) {
                    return redirect()->back()->with('error', 'Tidak dapat mengubah role. Ini adalah satu-satunya Super Admin!');
                }
            }

            $oldRole = $user->role;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

            if ($oldRole !== $request->role) {
                Log::info('User role changed', [
                    'admin_id' => auth()->id(),
                    'user_id' => $user->id,
                    'old_role' => $oldRole,
                    'new_role' => $request->role,
                ]);
            }

            return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
        });
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'role' => 'required|in:' . implode(',', self::VALID_ROLES),
        ]);

        // Cegah admin mengubah role diri sendiri
        if ($user->id === auth()->id() && $request->role !== $user->role) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
        }

        return DB::transaction(function () use ($request, $user) {
            // Cegah demosi super_admin terakhir dengan lockForUpdate
            if ($user->role === 'super_admin' && $request->role !== 'super_admin') {
                $superAdminCount = User::where('role', 'super_admin')->lockForUpdate()->count();
                if ($superAdminCount <= 1) {
                    return redirect()->back()->with('error', 'Tidak dapat mengubah role. Ini adalah satu-satunya Super Admin!');
                }
            }

            $oldRole = $user->role;

        $user->update([
            'role' => $request->role
        ]);

            Log::info('User role changed', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $request->role,
            ]);

            return redirect()->back()->with('success', 'Role pengguna berhasil diperbarui!');
        });
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        return DB::transaction(function () use ($user) {
            // Cegah penghapusan super_admin terakhir
            if ($user->role === 'super_admin') {
                $superAdminCount = User::where('role', 'super_admin')->lockForUpdate()->count();
                if ($superAdminCount <= 1) {
                    return redirect()->back()->with('error', 'Tidak dapat menghapus pengguna. Ini adalah satu-satunya Super Admin!');
                }
            }

            Log::info('User deleted', [
                'admin_id' => auth()->id(),
                'deleted_user_id' => $user->id,
                'deleted_user_role' => $user->role,
                'deleted_user_email' => $user->email,
            ]);

            $user->delete();

            return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
        });
    }
}
