<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Role yang diizinkan untuk tiap pintu login (portal).
     */
    private const PORTAL_ROLES = [
        'admin'     => ['admin', 'operator'],
        'walikelas' => ['walikelas'],
        'guru'      => ['guru'],
    ];

    /**
     * Menampilkan halaman login berdasarkan portal (admin | walikelas | guru).
     */
    public function create(string $portal = 'admin'): Response
    {
        // Pastikan portal yang diminta valid, jika tidak kembalikan 404
        abort_unless(isset(self::PORTAL_ROLES[$portal]), 404);

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'portal' => $portal,
        ]);
    }

    /**
     * Menangani proses autentikasi (login).
     */
    public function store(LoginRequest $request, string $portal = 'admin'): RedirectResponse
    {
        // Pastikan portal valid sebelum memproses login
        abort_unless(isset(self::PORTAL_ROLES[$portal]), 404);

        // Autentikasi user berdasarkan role yang diizinkan di portal tersebut
        $request->authenticate(self::PORTAL_ROLES[$portal]);

        // Regenerasi session untuk mencegah serangan session fixation
        $request->session()->regenerate();

        // Redirect berdasarkan role user yang berhasil login
        return match (auth()->user()->role) {
            'admin', 'operator' => redirect()->route('dashboard.index'),
            'walikelas', 'guru' => redirect()->route('dashboard.siswa'),
            default => redirect('/'),
        };
    }

    /**
     * Menghapus sesi autentikasi (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}