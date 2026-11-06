<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span>🤖</span> {{ __('Manajemen & Status AI Agents Multi-Tenant') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-sm text-indigo-900 flex items-start gap-3">
                <span class="text-xl">ℹ️</span>
                <div>
                    <strong>Konsep Isolasi Multi-Tenant:</strong>
                    Setiap dinas memiliki Agent AI dan Knowledge Base yang terisolasi 100%. Data kependudukan Dukcapil tidak akan pernah bocor ke pertanyaan seputar Pariwisata maupun Kominfo.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($agents as $agent)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $agent['dinas'] === 'dukcapil' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $agent['dinas'] === 'pariwisata' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $agent['dinas'] === 'kominfo' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ strtoupper($agent['dinas']) }}
                                </span>
                                <span class="flex items-center gap-1.5 text-xs text-green-600 font-semibold">
                                    <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                    {{ $agent['status'] }}
                                </span>
                            </div>

                            <h3 class="text-base font-bold text-gray-900 mb-2">
                                {{ $agent['name'] }}
                            </h3>

                            <p class="text-xs text-gray-600 mb-4">
                                {{ $agent['description'] }}
                            </p>

                            <div class="space-y-2 text-xs text-gray-500 pt-3 border-t border-gray-100">
                                <div class="flex justify-between">
                                    <span>Gaya Persona:</span>
                                    <span class="font-medium text-gray-800">{{ $agent['persona'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Dokumen Knowledge Base:</span>
                                    <span class="font-medium text-gray-800">{{ $agent['kb_docs'] }} Dokumen</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Agent ID Backend:</span>
                                    <code class="text-indigo-600">{{ $agent['id'] }}</code>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex gap-2">
                            <a href="{{ route('chatbot.index', ['dinas' => $agent['dinas']]) }}" class="flex-1 text-center py-2 px-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition">
                                💬 Uji Simulator
                            </a>
                            <a href="{{ url('/embed/' . $agent['dinas']) }}" target="_blank" class="py-2 px-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-xs font-semibold text-indigo-700 transition">
                                ↗ Widget
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Architecture Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <span>⚙️</span> Arsitektur Hybrid: Laravel + RAGFlow AI Engine
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-gray-600 mt-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900 block mb-1">1. Lapisan RBAC (Laravel)</strong>
                        Menjamin hak akses 3-level (Super Admin, Dinas Admin, Staff) sebelum pesan diteruskan ke mesin AI.
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900 block mb-1">2. Mesin AI & RAG (RAGFlow/S-Bot)</strong>
                        Melakukan pencarian semantik pada vektor dokumen SOP dinas yang bersangkutan secara air-gapped / offline.
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <strong class="text-gray-900 block mb-1">3. Standalone Embed Widget</strong>
                        Widget live chat responsif yang dapat ditempelkan di seluruh website resmi Pemkab Sleman.
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

