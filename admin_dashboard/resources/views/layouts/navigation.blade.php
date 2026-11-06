<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold text-lg text-indigo-700 flex items-center gap-2">
                        <span class="text-2xl">🤖</span>
                        <span>S-Bot Sleman</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex items-center text-sm">
                    @if(Auth::user()->isSuperAdmin())
                        <x-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                            {{ __('Dashboard Super') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.users.index')" :active="request()->routeIs('superadmin.users.*')">
                            {{ __('Kelola Pengguna') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.agents')" :active="request()->routeIs('superadmin.agents')">
                            {{ __('Status AI Agents') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.chat-history')" :active="request()->routeIs('superadmin.chat-history')">
                            {{ __('Semua Riwayat') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.analytics')" :active="request()->routeIs('superadmin.analytics')">
                            {{ __('Analitik') }}
                        </x-nav-link>
                    @elseif(Auth::user()->isDinasAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard Dinas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.staff')" :active="request()->routeIs('admin.staff')">
                            {{ __('Staff Dinas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.chat-history')" :active="request()->routeIs('admin.chat-history')">
                            {{ __('Riwayat Dinas') }}
                        </x-nav-link>
                    @endif

                    <!-- Akses Chatbot Simulator -->
                    <x-nav-link :href="route('chatbot.index')" :active="request()->routeIs('chatbot.index')">
                        {{ __('Chatbot Simulator') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Role Badge & Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Badge Role & Dinas -->
                <div class="flex items-center gap-2 text-xs">
                    @if(Auth::user()->isSuperAdmin())
                        <span class="bg-purple-100 text-purple-800 font-semibold px-2.5 py-1 rounded-full border border-purple-200">
                            🛡️ Super Admin (Diskominfo)
                        </span>
                    @elseif(Auth::user()->isDinasAdmin())
                        <span class="bg-blue-100 text-blue-800 font-semibold px-2.5 py-1 rounded-full border border-blue-200">
                            🏢 Admin {{ strtoupper(Auth::user()->dinas) }}
                        </span>
                    @else
                        <span class="bg-gray-100 text-gray-800 font-semibold px-2.5 py-1 rounded-full border border-gray-200">
                            👤 Staff {{ strtoupper(Auth::user()->dinas ?? 'Umum') }}
                        </span>
                    @endif
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-x-0 space-y-1">
            @if(Auth::user()->isSuperAdmin())
                <x-responsive-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                    {{ __('Dashboard Super') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('superadmin.users.index')" :active="request()->routeIs('superadmin.users.*')">
                    {{ __('Kelola Pengguna') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('superadmin.agents')" :active="request()->routeIs('superadmin.agents')">
                    {{ __('Status AI Agents') }}
                </x-responsive-nav-link>
            @elseif(Auth::user()->isDinasAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard Dinas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.staff')" :active="request()->routeIs('admin.staff')">
                    {{ __('Staff Dinas') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('chatbot.index')" :active="request()->routeIs('chatbot.index')">
                {{ __('Chatbot Simulator') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
