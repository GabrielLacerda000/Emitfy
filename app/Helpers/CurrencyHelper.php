<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Currency formatting configuration
     */
    private static array $config = [
        'BRL' => [
            'symbol' => 'R$',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'symbol_position' => 'before', // before or after
        ],
        'USD' => [
            'symbol' => '$',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'symbol_position' => 'before',
        ],
        'EUR' => [
            'symbol' => '€',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'symbol_position' => 'after',
        ],
        'GBP' => [
            'symbol' => '£',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'symbol_position' => 'before',
        ],
    ];

    /**
     * Format a currency amount with symbol
     *
     * @param  float|string  $amount  The amount to format
     * @param  string  $currency  The currency code (BRL, USD, EUR, GBP)
     * @return string Formatted currency string with symbol
     */
    public static function format(float|string $amount, string $currency = 'BRL'): string
    {
        $config = self::$config[$currency] ?? self::$config['USD'];
        $formatted = self::formatNumber($amount, $currency);

        if ($config['symbol_position'] === 'before') {
            return $config['symbol'].' '.$formatted;
        }

        return $formatted.' '.$config['symbol'];
    }

    /**
     * Format a number without currency symbol (for table cells)
     *
     * @param  float|string  $amount  The amount to format
     * @param  string  $currency  The currency code (for locale detection)
     * @return string Formatted number string without symbol
     */
    public static function formatNumber(float|string $amount, string $currency = 'BRL'): string
    {
        $config = self::$config[$currency] ?? self::$config['USD'];

        // Convert to float and ensure 2 decimal places
        $amount = number_format((float) $amount, 2, '.', '');

        // Split into integer and decimal parts
        [$integer, $decimal] = explode('.', $amount);

        // Format integer part with thousands separator
        $integer = strrev(implode($config['thousands_separator'], str_split(strrev($integer), 3)));

        // Combine with decimal separator
        return $integer.$config['decimal_separator'].$decimal;
    }
}
