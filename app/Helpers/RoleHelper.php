<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RoleHelper
{
    public static function is($roleName)
    {
        $user = Auth::user();
        if (!$user || !$user->activeRole) return false;

        return strtolower($user->activeRole->name) === strtolower($roleName);
    }
}
