<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        parent::boot();

    // ✅ Pastikan binding Role pakai kolom 'id' (UUID)
        Route::bind('role', function ($value) {
            return Role::where('id', $value)->firstOrFail();
        });

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
            ->group(base_path('routes/auth.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
