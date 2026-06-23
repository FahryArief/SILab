<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika user belum login, tendang ke halaman login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Jika role user ada di dalam daftar yang diizinkan, silakan masuk
        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        // Jika role tidak sesuai, tampilkan error 403 (Akses Ditolak)
        abort(403, 'Maaf, Anda tidak memiliki akses ke halaman ini!');
    }
}
