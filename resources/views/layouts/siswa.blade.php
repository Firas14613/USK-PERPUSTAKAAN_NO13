<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>@yield('title', 'Panel Siswa') - {{ config('app.name', 'Perpustakaan') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-surface text-on-surface antialiased">
        <div class="min-h-screen flex">
            <aside class="h-screen w-64 fixed left-0 top-0 bg-slate-100 dark:bg-slate-900 flex flex-col p-4 z-50">
                <div class="mb-10 px-4">
                    <h1 class="text-lg font-black text-blue-900 dark:text-white">SMKN 1 Purwokerto</h1>
                    <p class="text-xs font-medium tracking-wide uppercase text-on-surface-variant/70">The Intellectual Atelier</p>
                </div>

                <nav class="flex-1 space-y-2">
                    @php
                        $nav = [
                            ['label' => 'Dashboard', 'icon' => 'dashboard', 'href' => route('siswa.dashboard'), 'active' => request()->routeIs('siswa.dashboard')],
                            ['label' => 'Koleksi Buku', 'icon' => 'book_5', 'href' => route('siswa.koleksi'), 'active' => request()->routeIs('siswa.koleksi')],
                            ['label' => 'Riwayat Pinjam', 'icon' => 'history', 'href' => route('siswa.riwayat'), 'active' => request()->routeIs('siswa.riwayat')],
                            ['label' => 'Profil Saya', 'icon' => 'account_circle', 'href' => route('siswa.profil.edit'), 'active' => request()->routeIs('siswa.profil.*')],
                        ];
                    @endphp

                    @foreach ($nav as $item)
                        <a
                            href="{{ $item['href'] }}"
                            class="{{ $item['active'] ? 'flex items-center gap-3 bg-blue-100 dark:bg-blue-900/50 text-blue-900 dark:text-blue-100 rounded-full px-4 py-2 font-bold scale-95 transition-all duration-150' : 'flex items-center gap-3 text-slate-500 dark:text-slate-400 hover:text-blue-800 dark:hover:text-blue-200 px-4 py-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors duration-200' }}"
                        >
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">
                                {{ $item['icon'] }}
                            </span>
                            <span class="text-sm font-medium tracking-wide uppercase">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="pt-4 border-t border-outline-variant/20">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-surface-container-low hover:bg-surface-container-high transition-colors text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;">logout</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <main class="flex-1 ml-64">
                <div class="px-8 pt-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-outline">Panel Siswa</p>
                            <h2 class="text-3xl font-black tracking-tight text-primary">@yield('heading', 'Dashboard')</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-widest text-outline">Akun</p>
                            <p class="text-sm font-semibold text-on-surface">{{ auth()->user()->nama_lengkap ?? auth()->user()->username }}</p>
                        </div>
                    </div>

                    @if (session('toast'))
                        @php($toast = session('toast'))
                        <div class="mt-6 rounded-xl px-5 py-4 {{ ($toast['type'] ?? '') === 'success' ? 'bg-secondary-fixed text-on-secondary-fixed' : 'bg-error-container text-on-error-container' }}">
                            <p class="text-sm font-semibold">{{ $toast['message'] ?? '' }}</p>
                        </div>
                    @endif
                </div>

                <div class="px-8 pb-16">
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>

