<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-label uppercase tracking-[0.2em] text-outline mb-2">Reset Password</p>
        <h1 class="text-2xl font-bold tracking-tight text-on-surface">Buat Password Baru</h1>
        <p class="text-sm text-on-surface-variant mt-2 leading-relaxed">
            Masukkan password baru untuk akun kamu.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="ghost-border group transition-all duration-300">
            <label class="block text-xs font-label uppercase tracking-widest text-outline group-focus-within:text-primary mb-2" for="email">
                Email
            </label>
            <div class="flex items-center pb-3">
                <span class="material-symbols-outlined text-outline group-focus-within:text-primary mr-3 text-xl">alternate_email</span>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autofocus
                    value="{{ old('email', $request->email) }}"
                    autocomplete="username"
                    placeholder="user@skanesa.sch.id"
                    class="w-full bg-transparent border-none p-0 focus:ring-0 text-on-surface placeholder:text-outline-variant font-body"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="ghost-border group transition-all duration-300 mt-6">
            <label class="block text-xs font-label uppercase tracking-widest text-outline group-focus-within:text-primary mb-2" for="password">
                Password Baru
            </label>
            <div class="flex items-center pb-3">
                <span class="material-symbols-outlined text-outline group-focus-within:text-primary mr-3 text-xl">lock_reset</span>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Min. 8 karakter"
                    class="w-full bg-transparent border-none p-0 focus:ring-0 text-on-surface placeholder:text-outline-variant font-body"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="ghost-border group transition-all duration-300 mt-6">
            <label class="block text-xs font-label uppercase tracking-widest text-outline group-focus-within:text-primary mb-2" for="password_confirmation">
                Konfirmasi Password
            </label>
            <div class="flex items-center pb-3">
                <span class="material-symbols-outlined text-outline group-focus-within:text-primary mr-3 text-xl">lock</span>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password baru"
                    class="w-full bg-transparent border-none p-0 focus:ring-0 text-on-surface placeholder:text-outline-variant font-body"
                />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-8 space-y-4">
            <button class="w-full editorial-gradient py-4 rounded-xl text-white font-semibold text-base hover:shadow-[0_20px_40px_rgba(0,35,111,0.15)] transition-all transform active:scale-[0.98]" type="submit">
                Simpan Password Baru
            </button>

            <a href="{{ route('login') }}" class="block text-center text-sm font-medium text-primary hover:text-primary-container transition-colors">
                Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>
