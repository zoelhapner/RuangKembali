<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class ActiveRole
{
public static function role()
{
    $user = Auth::user();

    return $user->activeRole
        ?? $user->roles()->first();
}


    public static function name()
    {
        return self::role()?->name;
    }

    public static function permissions()
{
    $user = Auth::user();

    // fallback kalau activeRole belum ada
    if (!$user->activeRole) {
        return $user->getAllPermissions()->pluck('name')->toArray();
    }

    return $user->activeRole->permissions->pluck('name')->toArray();
}


    public static function hasPermission($permission)
    {
        return in_array($permission, self::permissions());
    }
}