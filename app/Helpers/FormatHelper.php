<?php

namespace App\Helpers;
use Carbon\Carbon;

class FormatHelper
{
    public static function cleanFk($value): string
    {
        if (is_null($value)) {
            return '';
        }

        // Trim, ganti tab/newline jadi spasi tunggal, hapus spasi ganda
        return preg_replace('/\s+/', ' ', trim($value));
    }

    public static function parseIndoDate($value)
    {
        $value = trim($value ?? '');

        if (!$value) return null;

        // Ganti bulan Indonesia ke Inggris
        $indo = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $en = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $value = str_ireplace($indo, $en, $value);

        try {
            return Carbon::createFromFormat('d F Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e2) {
                logger('Parse Date Failed: ' . $value);
                return null;
            }
        }
    }

    public static function parseNumberOrDefault($value, $default = 0)
    {
        $value = trim($value ?? '');
        return is_numeric($value) ? $value : $default;
    }

    public static function parseTextOrDefault($value, $default = '-')
    {
        $value = trim($value ?? '');
        return $value !== '' ? $value : $default;
    }

    public static function detectRelationship(string $name): int
{
    $firstWord = strtolower(explode(' ', trim($name))[0] ?? '');

    switch ($firstWord) {
        case 'ayah':
        case 'bapak':
            return 1; // Ayah
        case 'ibu':
        case 'bunda':
            return 2; // Ibu
        case 'anak':
            return 3; // Anak
        case 'suami':
            return 4; // Suami
        case 'istri':
            return 5; // Istri
        default:
            return 3; // Default Anak
    }
}


  

}
