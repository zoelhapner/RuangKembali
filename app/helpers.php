<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('route_exists')) {
    function route_exists($name)
    {
        return Route::has($name);
    }
}