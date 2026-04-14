<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = (string) $request->input('email');

        $genericMessage = 'Jika email terdaftar, kami akan mengirim tautan reset password.';

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return back()->with('status', $genericMessage);
        }

        if ($user->role === 'siswa' && ($user->siswa?->status === 'keluar')) {
            return back()->with('status', $genericMessage);
        }

        Password::sendResetLink(['email' => $email]);

        return back()->with('status', $genericMessage);
    }
}
