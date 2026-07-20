<?php

namespace App\Services;

class SkuService
{
    public static function generate($category, $brand, $type, $size = null, $volume = null, $colors = [])
    {
        $cat = strtoupper(substr($category, 0, 3));
        $br  = strtoupper(substr($brand, 0, 3));
        $tp  = strtoupper(substr($type, 0, 3));

        $size = $size ? strtoupper($size) : null;
        $vol  = $volume ? strtoupper($volume) : null;

        $clr = null;
        if (!empty($colors)) {
            $clr = strtoupper(substr($colors[0], 0, 3));
        }

        $rand = rand(1000, 9999);

        $parts = array_filter([$cat, $br, $tp, $size, $vol, $clr, $rand]);

        return implode('-', $parts);
    }
}
