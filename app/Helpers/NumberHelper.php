<?php
if (!function_exists('number_to_letters')) {
    function number_to_letters($num) {
        $letters = '';
        $num = $num + 1;

        while ($num > 0) {
            $rem = ($num - 1) % 26;
            $letters = chr(65 + $rem) . $letters;
            $num = intdiv(($num - 1), 26);
        }

        return $letters;
    }
}
if (!function_exists('alphaIndex')) {
    function alphaIndex(int $index): string
    {
        $result = '';
        do {
            $result = chr(65 + ($index % 26)) . $result;
            $index  = intdiv($index, 26) - 1;
        } while ($index >= 0);
        return $result;
    }
}