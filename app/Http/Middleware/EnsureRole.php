<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pemakaian di route: ->middleware('role:admin,operator')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // Walikelas/guru yang nyasar ke halaman admin dikembalikan ke halaman siswa
        if ($user->isTeacher() && $request->isMethod('GET')) {
            return redirect()
                ->route('dashboard.siswa')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
