<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'S-Bot Sleman') }} — Portal AI Multi-Tenant Pemkab Sleman</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Flowbite -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#eef2ff',
                                100: '#e0e7ff',
                                500: '#6366f1',
                                600: '#4f46e5',
                                700: '#4338ca',
                                800: '#3730a3',
                                900: '#1e1b4b',
                            },
                            sleman: {
                                dark: '#0f172a',
                                navy: '#1e293b',
                                blue: '#2563eb',
                                gold: '#f59e0b',
                            }
                        }
                    }
                }
            }
        </script>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            * {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            /* Custom Smooth Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    </head>
    <body class="h-full bg-slate-50 text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Sleman Footer -->
            <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
                <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row justify-between items-center gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-700">S-Bot Multi-Tenant</span>
                        <span>•</span>
                        <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                    </div>
                    <div class="text-slate-400">
                        Sistem AI Air-Gapped & Isolasi Data Lokal © {{ date('Y') }}
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
