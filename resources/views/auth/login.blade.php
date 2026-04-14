<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Login - {{ config('app.name', 'Perpustakaan') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-surface text-on-surface font-body selection:bg-primary-fixed selection:text-on-primary-fixed">
        <main class="min-h-screen flex flex-col md:flex-row overflow-hidden">
            <section class="hidden md:flex md:w-1/2 relative overflow-hidden bg-primary items-center justify-center">
                <div class="absolute inset-0 z-0 opacity-20">
                    <div class="w-full h-full bg-gradient-to-br from-white/20 to-transparent"></div>
                </div>
                <div class="relative z-10 px-14">
                    <p class="text-xs font-label uppercase tracking-[0.2em] text-primary-fixed-dim mb-6">Library Atelier</p>
                    <h2 class="text-4xl font-extrabold text-white tracking-tight mb-4">Selamat Datang di Library Atelier</h2>
                    <p class="text-white/80 text-base leading-relaxed max-w-md">Silakan masuk ke akun Anda untuk melanjutkan penjelajahan literatur.</p>
                </div>
            </section>

            <section class="flex-1 flex flex-col bg-surface">
                <div class="flex-1 flex items-center justify-center px-8 py-12">
                    <div class="w-full max-w-md">
                        <div class="mb-10">
                            <p class="text-xs font-label uppercase tracking-widest text-outline mb-2">Skanesa Scholastic</p>
                            <h1 class="text-3xl font-black tracking-tight text-primary">Masuk</h1>
                            <p class="text-sm text-on-surface-variant mt-2">Gunakan username atau email sekolah.</p>
                        </div>

                        <x-auth-session-status class="mb-6" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf

                            <div class="space-y-8">
                                <div class="ghost-border group transition-all duration-300">
                                    <label class="block text-xs font-label uppercase tracking-widest text-outline group-focus-within:text-primary mb-2" for="login">
                                        Username atau Email
                                    </label>
                                    <div class="flex items-center pb-3">
                                        <span class="material-symbols-outlined text-outline group-focus-within:text-primary mr-3 text-xl">alternate_email</span>
                                        <input class="w-full bg-transparent border-none p-0 focus:ring-0 text-on-surface placeholder:text-outline-variant font-body" id="login" name="login" placeholder="user@skanesa.sch.id" type="text" value="{{ old('login') }}" required autofocus autocomplete="username" />
                                    </div>
                                    <x-input-error :messages="$errors->get('login')" class="mt-2" />
                                </div>

                                <div class="ghost-border group transition-all duration-300">
                                    <label class="block text-xs font-label uppercase tracking-widest text-outline group-focus-within:text-primary mb-2" for="password">
                                        Password
                                    </label>
                                    <div class="flex items-center pb-3">
                                        <span class="material-symbols-outlined text-outline group-focus-within:text-primary mr-3 text-xl">lock_open</span>
                                        <input class="w-full bg-transparent border-none p-0 focus:ring-0 text-on-surface placeholder:text-outline-variant font-body" id="password" name="password" placeholder="••••••••" type="password" required autocomplete="current-password" />
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <label class="flex items-center cursor-pointer group">
                                    <input name="remember" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20 transition-all" type="checkbox" />
                                    <span class="ml-2 text-sm text-on-surface-variant group-hover:text-on-surface transition-colors">Ingat Saya</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-sm font-medium text-primary hover:text-primary-container transition-colors" href="{{ route('password.request') }}">
                                        Lupa Password?
                                    </a>
                                @endif
                            </div>

                            <button class="w-full editorial-gradient py-4 rounded-xl text-white font-semibold text-lg hover:shadow-[0_20px_40px_rgba(0,35,111,0.15)] transition-all transform active:scale-[0.98] mt-4" type="submit">
                                Masuk
                            </button>
                        </form>

                        <div class="pt-10 border-t border-outline-variant/10 mt-10">
                            <div class="bg-surface-container-low p-4 rounded-xl flex items-start gap-4">
                                <div class="h-10 w-10 rounded-full bg-surface-container-lowest flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-primary text-xl">support_agent</span>
                                </div>
                                <div>
                                    <p class="text-xs font-label uppercase tracking-wider text-outline mb-1">Butuh Bantuan?</p>
                                    <p class="text-xs text-on-surface-variant leading-relaxed">
                                        Mengalami kendala saat masuk? Silakan hubungi pustakawan untuk bantuan teknis.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="w-full px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4 bg-surface-container-low text-sm tracking-wide uppercase opacity-80">
                    <span class="text-on-surface-variant">© {{ now()->year }} SMKN 1 Purwokerto Library Information System</span>
                    <div class="flex gap-6">
                        <span class="text-on-surface-variant">Privacy</span>
                        <span class="text-on-surface-variant">Terms</span>
                    </div>
                </footer>
            </section>
        </main>
    </body>
</html>
