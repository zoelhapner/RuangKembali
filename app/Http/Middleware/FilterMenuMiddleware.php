<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FilterMenuMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login
        if (Auth::check()) {
            $user = Auth::user();
            Log::info('🧩 FilterMenuMiddleware: jalankan filter untuk user', ['user' => $user->fullname ?? 'unknown']);

            // Ambil menu yang sudah difilter dari MenuServiceProvider
            $menu = config('tablar.menu', []);

            // Filter ulang jika dibutuhkan (optional, tapi aman)
            $menu = $this->filterMenuByUser($menu, $user);

            config(['tablar.menu' => $menu]);
        } else {
            Log::info('⏩ FilterMenuMiddleware: user belum login, skip');
        }

        return $next($request);
    }

    protected function filterMenuByUser(array $menu, $user): array
    {
        return array_values(array_filter(array_map(function ($item) use ($user) {
            if (isset($item['submenu'])) {
                $item['submenu'] = $this->filterMenuByUser($item['submenu'], $user);
                if (empty($item['submenu'])) {
                    return null;
                }
            }

            if (isset($item['can']) && !$user->can($item['can'])) {
                return null;
            }

            if (isset($item['roles']) && !$user->hasAnyRole($item['roles'])) {
                return null;
            }

            return $item;
        }, $menu)));
    }
}
