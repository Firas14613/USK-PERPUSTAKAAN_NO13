<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SiswaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'siswa') {
            abort(403);
        }

        $status = $user->siswa?->status;

        if ($status === 'keluar') {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'login' => 'Akun sudah keluar, tidak dapat login. Hubungi admin.',
            ]);
        }

        if ($status === 'lulus' && $request->isMethod('post')) {
            if ($request->routeIs('siswa.pinjam') || $request->routeIs('siswa.profil.update')) {
                return back()->with('toast', [
                    'type' => 'danger',
                    'message' => 'Status kamu LULUS, akun hanya bisa melihat data (read-only).',
                ]);
            }
        }

        return $next($request);
    }
}
