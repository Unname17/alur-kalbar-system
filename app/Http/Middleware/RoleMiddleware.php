<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Cek apakah peran user ada dalam daftar role yang diizinkan
$userRole = strtolower($user->role->name ?? ''); 

    if (in_array($userRole, array_map('strtolower', $roles))) {
        return $next($request);
    }

        // Jika tidak punya akses, arahkan kembali atau beri error 403
        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}