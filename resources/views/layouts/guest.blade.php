<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	    <head>
	        <meta charset="utf-8">
	        <meta name="viewport" content="width=device-width, initial-scale=1">
	        <meta name="csrf-token" content="{{ csrf_token() }}">

	        <title>{{ config('app.name', 'Perpustakaan') }}</title>

	        <link rel="preconnect" href="https://fonts.googleapis.com" />
	        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
	        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet" />

	        <!-- Scripts -->
	        @vite(['resources/css/app.css', 'resources/js/app.js'])
	    </head>
	    <body class="bg-surface text-on-surface font-body selection:bg-primary-fixed selection:text-on-primary-fixed">
	        <main class="min-h-screen flex items-center justify-center px-6 py-10">
	            <div class="w-full max-w-md">
	                <a href="{{ route('login') }}" class="inline-flex items-center gap-3 mb-8">
	                    <div class="h-11 w-11 rounded-2xl bg-primary flex items-center justify-center text-white shadow-sm">
	                        <span class="material-symbols-outlined">local_library</span>
	                    </div>
	                    <div>
	                        <p class="text-xs font-label uppercase tracking-[0.2em] text-outline">Library Atelier</p>
	                        <p class="text-sm font-semibold text-on-surface">{{ config('app.name', 'Perpustakaan') }}</p>
	                    </div>
	                </a>

	                <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-[0_24px_70px_rgba(0,0,0,0.07)] p-8">
	                    {{ $slot }}
	                </div>

	                <p class="mt-6 text-center text-xs text-on-surface-variant/80">
	                    © {{ now()->year }} SMKN 1 Purwokerto
	                </p>
	            </div>
	        </main>
	    </body>
	</html>
