<?php

namespace App\Helpers;

class UnitHelper
{
    public static function convertToBaseUnit($product, $qty, $unitLevel)
    {
        if ($unitLevel == 1) {
            return $qty * $product->unit_1_value;
        }
        if ($unitLevel == 2) {
            return $qty * $product->unit_2_value;
        }
        if ($unitLevel == 3) {
            return $qty * $product->unit_3_value;
        }
        if ($unitLevel == 4) {
            return $qty * $product->unit_4_value;
        }

        return $qty;
    }
}
