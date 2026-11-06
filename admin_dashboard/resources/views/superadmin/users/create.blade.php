<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pengguna Baru') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">

                @if ($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Instansi</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role Sistem</label>
                        <select name="role" id="roleSelect" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin (Diskominfo Sleman)</option>
                            <option value="dinas_admin" {{ old('role') == 'dinas_admin' ? 'selected' : '' }}>Admin Dinas</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff Operator</option>
                        </select>
                    </div>

                    <div id="dinasContainer">
                        <label class="block text-sm font-medium text-gray-700">Dinas Terikat</label>
                        <select name="dinas" id="dinasSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Pilih Dinas --</option>
                            <option value="kominfo" {{ old('dinas') == 'kominfo' ? 'selected' : '' }}>Dinas Kominfo</option>
                            <option value="pariwisata" {{ old('dinas') == 'pariwisata' ? 'selected' : '' }}>Dinas Pariwisata</option>
                            <option value="dukcapil" {{ old('dinas') == 'dukcapil' ? 'selected' : '' }}>Dinas Dukcapil</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika role Super Admin.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

