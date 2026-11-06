<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Pengguna (Multi-Tenant RBAC)') }}
            </h2>
            <a href="{{ route('superadmin.users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                + Tambah Pengguna
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Filter Card -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center justify-between">
                <form method="GET" action="{{ route('superadmin.users.index') }}" class="flex flex-wrap gap-3 items-center">
                    <div>
                        <select name="role" class="rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Semua Role --</option>
                            <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="dinas_admin" {{ request('role') == 'dinas_admin' ? 'selected' : '' }}>Admin Dinas</option>
                            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div>
                        <select name="dinas" class="rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Semua Dinas --</option>
                            <option value="kominfo" {{ request('dinas') == 'kominfo' ? 'selected' : '' }}>Kominfo</option>
                            <option value="pariwisata" {{ request('dinas') == 'pariwisata' ? 'selected' : '' }}>Pariwisata</option>
                            <option value="dukcapil" {{ request('dinas') == 'dukcapil' ? 'selected' : '' }}>Dukcapil</option>
                        </select>
                    </div>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="rounded-lg border-gray-300 text-xs w-60 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-800 text-white rounded-lg text-xs font-semibold hover:bg-gray-700">
                        Filter
                    </button>
                    @if(request()->hasAny(['role', 'dinas', 'search']))
                        <a href="{{ route('superadmin.users.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Nama</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-left">Dinas Terikat</th>
                            <th class="px-6 py-3 text-left">Tanggal Buat</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $u)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ $u->name }}
                                    @if($u->id === auth()->id())
                                        <span class="text-xs text-indigo-600 font-normal ml-1">(Anda)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $u->email }}</td>
                                <td class="px-6 py-4">
                                    @if($u->role === 'super_admin')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                            Super Admin
                                        </span>
                                    @elseif($u->role === 'dinas_admin')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Dinas Admin
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            Staff
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($u->dinas)
                                        <span class="capitalize font-medium text-gray-700">{{ $u->dinas }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">Semua Dinas (Super)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $u->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('superadmin.users.edit', $u) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('superadmin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-900">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    Tidak ada pengguna ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $users->links() }}
            </div>

        </div>
    </div>
</x-app-layout>

