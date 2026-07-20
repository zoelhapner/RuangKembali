<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::info('✅ MenuServiceProvider: boot() dipanggil');

        // Ambil menu original dari config/tablar.php
        $menu = config('tablar.menu', []);

        // Jika user belum login, skip filter
        if (!Auth::check()) {
            Log::info('🔸 MenuServiceProvider: user belum login, lewati filter menu');
            config(['tablar.menu' => $menu]);
            return;
        }

        $user = Auth::user();
        $filteredMenu = $this->filterMenuByUser($menu, $user);

        // Ganti menu bawaan dengan yang sudah difilter
        config(['tablar.menu' => $filteredMenu]);

        Log::info('✅ MenuServiceProvider: menu difilter untuk user', [
            'user' => $user->name ?? 'unknown',
            'count' => count($filteredMenu),
        ]);
    }

    /**
     * Filter menu berdasarkan role dan permission user.
     */
    protected function filterMenuByUser(array $menu, $user): array
    {
        return array_values(array_filter(array_map(function ($item) use ($user) {
            // Jika punya submenu, filter rekursif
            if (isset($item['submenu'])) {
                $item['submenu'] = $this->filterMenuByUser($item['submenu'], $user);
                if (empty($item['submenu'])) {
                    return null;
                }
            }

            // Jika item punya "can" (permission) cek user permission
            if (isset($item['can']) && !$user->can($item['can'])) {
                return null;
            }

            // Jika item punya "roles", pastikan user punya salah satu
            if (isset($item['roles']) && !$user->hasAnyRole($item['roles'])) {
                return null;
            }

            return $item;
        }, $menu)));
    }
}
