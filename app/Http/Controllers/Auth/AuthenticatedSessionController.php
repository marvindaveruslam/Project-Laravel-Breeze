<?php

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
    /** Role yang diizinkan untuk tiap pintu login */
    private const PORTAL_ROLES = [
        'admin'     => ['admin', 'operator'],
        'walikelas' => ['walikelas'],
        'guru'      => ['guru'],
    ];

    /**
     * Display the login view (portal: admin | walikelas | guru).
     */
    public function create(string $portal = 'admin'): Response
    {
        abort_unless(isset(self::PORTAL_ROLES[$portal]), 404);

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'portal' => $portal,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, string $portal = 'admin'): RedirectResponse
    {
        abort_unless(isset(self::PORTAL_ROLES[$portal]), 404);

        $request->authenticate(self::PORTAL_ROLES[$portal]);

        $request->session()->regenerate();

        return match (auth()->user()->role) {
            'admin', 'operator' => redirect()->route('dashboard.index'),
            'walikelas', 'guru' => redirect()->route('dashboard.siswa'),
            default => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
