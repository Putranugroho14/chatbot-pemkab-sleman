<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDinasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $requiredRole = null): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // Super Admin bypass semua pembatasan
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Validasi role jika ditentukan di route middleware
        if ($requiredRole && $user->role !== $requiredRole) {
            abort(403, 'Akses ditolak: Anda tidak memiliki role yang sesuai (' . $requiredRole . ').');
        }

        // Ambil parameter dinas dari input request, parameter route, atau query string
        $requestedDinas = $request->input('dinas') 
            ?? $request->route('dinas') 
            ?? $request->query('dinas');

        if ($requestedDinas && !$user->canAccessDinas($requestedDinas)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki akses ke dinas: ' . htmlspecialchars($requestedDinas));
        }

        return $next($request);
    }
}

