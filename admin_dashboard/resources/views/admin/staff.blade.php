<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Staff: Dinas ') }} {{ ucfirst($dinas) }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Tambah Staff -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span>➕</span> Daftarkan Staff Baru
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Staff yang ditambahkan akan otomatis terikat ke Dinas {{ ucfirst($dinas) }} dan hanya dapat menggunakan simulator chat dinas ini.
                    </p>

                    <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Nama Staff</label>
                            <input type="text" name="name" required placeholder="Nama lengkap staff" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Email Instansi</label>
                            <input type="email" name="email" required placeholder="staff.nama@slemankab.go.id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Password</label>
                            <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700 transition">
                            Simpan Staff Dinas
                        </button>
                    </form>
                </div>

                <!-- Daftar Staff Table -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-900">
                            Daftar Staff Aktif (Dinas {{ ucfirst($dinas) }})
                        </h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-left">Role</th>
                                <th class="px-6 py-3 text-left">Tanggal Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($staffList as $staff)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $staff->name }}</td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">{{ $staff->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            Staff
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $staff->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                        Belum ada staff terdaftar untuk dinas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4">
                        {{ $staffList->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

