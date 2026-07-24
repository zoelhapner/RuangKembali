<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next, $roleName): Response
    // {
    //     $active = auth()->user()->activeRole?->name;

    //     if ($active !== $roleName) {
    //         abort(403, 'Akses ditolak. Role aktif tidak sesuai.');
    //     }

    //     return $next($request);
    // }

public function handle(Request $request, Closure $next, ...$roleNames): Response
{
    if (!auth()->check()) {
        abort(403, 'Unauthorized.');
    }

    $user = auth()->user();

    // Super role selalu boleh
    if ($user->hasAnyRole([
        'Super-Admin',
        'Tim',
    ])) {
        return $next($request);
    }

    $activeRole = strtolower(optional($user->activeRole)->name);

    if (!$activeRole) {
        abort(403, 'Tidak ada active role yang dipilih.');
    }

    $allowed = collect($roleNames)
        ->map(fn ($role) => strtolower(trim($role)));

    if (!$allowed->contains($activeRole)) {
        abort(403, 'Role aktif tidak sesuai.');
    }

    return $next($request);
}
}
