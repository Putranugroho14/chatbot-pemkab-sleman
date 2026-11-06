<nav x-data="{ open: false }" class="bg-slate-900 border-b border-slate-800 text-white shadow-lg sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                <!-- Logo & Brand Identity -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-105 transition duration-200">
                            <span class="text-xl">🏛️</span>
                        </div>
                        <div>
                            <span class="font-extrabold text-base tracking-tight text-white block leading-none">
                                S-Bot <span class="text-indigo-400">Sleman</span>
                            </span>
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">
                                Multi-Tenant AI Platform
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-1 ms-4 text-xs font-semibold">
                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            📊 Dashboard
                        </a>
                        <a href="{{ route('superadmin.users.index') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('superadmin.users.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            👥 Pengguna
                        </a>
                        <a href="{{ route('superadmin.agents') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('superadmin.agents') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            🤖 Status Agents
                        </a>
                        <a href="{{ route('superadmin.chat-history') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('superadmin.chat-history') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            📋 Log Audit
                        </a>
                        <a href="{{ route('superadmin.analytics') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('superadmin.analytics') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            📈 Analitik
                        </a>
                    @elseif(Auth::user()->isDinasAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            🏢 Dashboard Dinas
                        </a>
                        <a href="{{ route('admin.staff') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('admin.staff') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            👥 Staff Dinas
                        </a>
                        <a href="{{ route('admin.chat-history') }}" 
                           class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('admin.chat-history') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            📋 Riwayat Dinas
                        </a>
                    @endif

                    <!-- Simulator Link (Tersedia untuk semua role) -->
                    <a href="{{ route('chatbot.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('chatbot.index') ? 'bg-emerald-600 text-white shadow-sm' : 'text-emerald-400 hover:text-white hover:bg-slate-800' }}">
                        <span>💬</span> Chat Simulator
                    </a>
                </div>
            </div>

            <!-- Role Badge & User Profile -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Role Badge -->
                @if(Auth::user()->isSuperAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-300 border border-purple-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                        Super Admin Diskominfo
                    </span>
                @elseif(Auth::user()->isDinasAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-300 border border-blue-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        Admin {{ strtoupper(Auth::user()->dinas) }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-700/50 text-slate-300 border border-slate-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Staff {{ strtoupper(Auth::user()->dinas ?? 'Umum') }}
                    </span>
                @endif

                <!-- User Dropdown Menu -->
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-800 transition focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-xs uppercase shadow">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <span class="text-xs font-medium text-slate-200 hidden lg:inline">{{ Auth::user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="userMenuOpen" 
                         @click.outside="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 rounded-xl bg-white text-slate-800 shadow-xl border border-slate-100 py-1 z-50">
                        
                        <div class="px-4 py-2 border-b border-slate-100 text-xs">
                            <p class="font-semibold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                            <span>👤</span> Profil Akun
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-red-50 text-left transition">
                                <span>🚪</span> Keluar (Log Out)
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = !open" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden bg-slate-950 border-t border-slate-800 p-4 space-y-2 text-sm">
        @if(Auth::user()->isSuperAdmin())
            <a href="{{ route('superadmin.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Dashboard</a>
            <a href="{{ route('superadmin.users.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Kelola Pengguna</a>
            <a href="{{ route('superadmin.agents') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Status Agents</a>
            <a href="{{ route('superadmin.chat-history') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Semua Riwayat</a>
        @elseif(Auth::user()->isDinasAdmin())
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Dashboard Dinas</a>
            <a href="{{ route('admin.staff') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Staff Dinas</a>
            <a href="{{ route('admin.chat-history') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800">Riwayat Dinas</a>
        @endif
        <a href="{{ route('chatbot.index') }}" class="block px-3 py-2 rounded-lg text-emerald-400 hover:bg-slate-800 font-semibold">💬 Chat Simulator</a>
        <div class="pt-3 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-red-400 font-semibold px-3 py-1">Keluar (Log Out)</button>
            </form>
        </div>
    </div>
</nav>
