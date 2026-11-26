<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WalikelasMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Asumsi: user yang login adalah Guru (ada di model Guru)
        // Cek role guru
        if (auth()->check() && auth()->user()->role === 'walikelas') {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Akses ditolak! Hanya Walikelas yang bisa mengakses halaman ini.');
    }
}