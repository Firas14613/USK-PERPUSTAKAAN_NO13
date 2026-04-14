@extends('layouts.siswa')

@section('title', 'Profil')
@section('heading', 'Profil Saya')

@section('content')
    <div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-10">
        <section class="lg:col-span-7">
            <div class="bg-surface-container-low rounded-xl p-8">
                <div class="flex items-center justify-between gap-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-outline">Account Atelier</p>
                        <h3 class="text-2xl font-black text-primary mt-2">Informasi Akun</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-outline">Username</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $user->username }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('siswa.profil.update') }}" class="mt-8 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Nama Lengkap</label>
                            <input name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="w-full bg-surface-container-lowest border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-container transition-all" />
                            <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-1" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Email</label>
                            <input name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full bg-surface-container-lowest border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-container transition-all" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Telepon</label>
                            <input name="telepon" value="{{ old('telepon', $user->telepon) }}" class="w-full bg-surface-container-lowest border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-container transition-all" />
                            <x-input-error :messages="$errors->get('telepon')" class="mt-1" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Alamat</label>
                            <textarea name="alamat" rows="2" class="w-full bg-surface-container-lowest border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-container transition-all resize-none">{{ old('alamat', $user->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-2">
                        <button class="bg-primary hover:bg-primary-container text-on-primary px-8 py-3 rounded-md font-semibold text-sm transition-all shadow-md flex items-center gap-2" type="submit">
                            <span class="material-symbols-outlined text-sm">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="lg:col-span-5 flex flex-col gap-8">
            <div class="bg-primary-container/10 border border-primary/10 rounded-xl p-8 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary/5 rounded-full blur-2xl"></div>
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-primary">school</span>
                    <h3 class="text-xl font-semibold text-primary">Data Akademik</h3>
                </div>

                <div class="space-y-5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-on-surface-variant/70 uppercase">Nomor Induk Siswa (NIS)</span>
                        <span class="text-sm font-bold text-primary">{{ $siswa?->nis ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-on-surface-variant/70 uppercase">Kelas</span>
                        <span class="text-sm font-bold text-on-surface">{{ $siswa?->kelas ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-on-surface-variant/70 uppercase">Jurusan</span>
                        <span class="text-sm font-bold text-on-surface text-right">{{ $siswa?->jurusan ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-on-surface-variant/70 uppercase">Tanggal Lahir</span>
                        <span class="text-sm font-bold text-on-surface">{{ $siswa?->tanggal_lahir ? $siswa->tanggal_lahir->format('d M Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-primary/10">
                        <span class="text-xs font-medium text-on-surface-variant/70 uppercase">Status Keanggotaan</span>
                        <span class="bg-secondary-fixed text-on-secondary-fixed text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">{{ strtoupper($siswa?->status ?? '—') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface-container-low rounded-xl p-8 border border-outline-variant/10">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-on-surface-variant">lock_reset</span>
                    <h3 class="text-lg font-semibold text-on-surface">Update Password</h3>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Password Saat Ini</label>
                        <input name="current_password" class="bg-surface-container-lowest border-none rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary-container transition-all" placeholder="••••••••" type="password" autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password') ?? []" class="mt-1" />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Password Baru</label>
                        <input name="password" class="bg-surface-container-lowest border-none rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary-container transition-all" placeholder="Min. 8 karakter" type="password" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password') ?? []" class="mt-1" />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Konfirmasi Password</label>
                        <input name="password_confirmation" class="bg-surface-container-lowest border-none rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary-container transition-all" placeholder="Ulangi password baru" type="password" autocomplete="new-password" />
                    </div>

                    <button class="w-full mt-4 py-3 border border-primary text-primary hover:bg-primary hover:text-on-primary rounded-lg font-bold text-xs uppercase tracking-widest transition-all" type="submit">
                        Ganti Password
                    </button>
                </form>
            </div>
        </section>
    </div>

    <section class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-high/50 p-6 rounded-lg flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-primary shadow-sm">
                <span class="material-symbols-outlined">menu_book</span>
            </div>
            <div>
                <p class="text-2xl font-black text-primary leading-none">{{ $bukuDipinjam }}</p>
                <p class="text-[10px] font-bold text-on-surface-variant uppercase mt-1">Buku Dipinjam</p>
            </div>
        </div>
        <div class="bg-surface-container-high/50 p-6 rounded-lg flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-on-tertiary-container shadow-sm">
                <span class="material-symbols-outlined">event_repeat</span>
            </div>
            <div>
                <p class="text-2xl font-black text-on-tertiary-container leading-none">{{ $jatuhTempo }}</p>
                <p class="text-[10px] font-bold text-on-surface-variant uppercase mt-1">Jatuh Tempo</p>
            </div>
        </div>
        <div class="bg-surface-container-high/50 p-6 rounded-lg flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-secondary shadow-sm">
                <span class="material-symbols-outlined">stars</span>
            </div>
            <div>
                <p class="text-2xl font-black text-secondary leading-none">4.8</p>
                <p class="text-[10px] font-bold text-on-surface-variant uppercase mt-1">Skor Literasi</p>
            </div>
        </div>
    </section>
@endsection
