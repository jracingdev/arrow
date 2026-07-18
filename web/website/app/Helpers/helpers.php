<?php

if (!function_exists('formatCurrency')) {
    /**
     * Format a price with currency symbol and decimal settings
     *
     * @param float $amount
     * @param array $currency ['symbol', 'decimal_degits', 'symbolAtRight']
     * @return string
     */
    function formatCurrency($amount, $currency = [])
    {
        $symbol = $currency['symbol'] ?? '';
        $decimals = $currency['decimal_degits'] ?? 2;
        $symbolAtRight = filter_var($currency['symbolAtRight'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $formatted = number_format($amount, $decimals);

        return $symbolAtRight
            ? $formatted . ' ' . $symbol
            : $symbol . $formatted;
    }
}