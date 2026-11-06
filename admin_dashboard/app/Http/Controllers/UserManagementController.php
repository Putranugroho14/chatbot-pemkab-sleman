<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * Tampilkan daftar semua pengguna.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('dinas')) {
            $query->where('dinas', $request->query('dinas'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Tampilkan form pembuatan user baru.
     */
    public function create()
    {
        return view('superadmin.users.create');
    }

    /**
     * Simpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
            'role' => ['required', Rule::in(['super_admin', 'dinas_admin', 'staff'])],
            'dinas' => ['nullable', Rule::in(['kominfo', 'pariwisata', 'dukcapil'])],
        ]);

        // Jika bukan super_admin, dinas wajib diisi
        if ($validated['role'] !== 'super_admin' && empty($validated['dinas'])) {
            return back()->withErrors(['dinas' => 'Dinas wajib dipilih untuk Dinas Admin dan Staff.'])->withInput();
        }

        if ($validated['role'] === 'super_admin') {
            $validated['dinas'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('superadmin.users.index')
            ->with('status', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit user.
     */
    public function edit(User $user)
    {
        return view('superadmin.users.edit', compact('user'));
    }

    /**
     * Update data user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', Rule::in(['super_admin', 'dinas_admin', 'staff'])],
            'dinas' => ['nullable', Rule::in(['kominfo', 'pariwisata', 'dukcapil'])],
        ]);

        if ($validated['role'] !== 'super_admin' && empty($validated['dinas'])) {
            return back()->withErrors(['dinas' => 'Dinas wajib dipilih untuk Dinas Admin dan Staff.'])->withInput();
        }

        if ($validated['role'] === 'super_admin') {
            $validated['dinas'] = null;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('superadmin.users.index')
            ->with('status', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Hapus user dari database.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('status', 'Pengguna berhasil dihapus.');
    }
}

