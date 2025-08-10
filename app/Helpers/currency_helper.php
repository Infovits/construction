<?php

/**
 * Currency Helper Functions for Malawi Kwacha (MWK)
 */

if (!function_exists('format_currency')) {
    /**
     * Format amount as Malawi Kwacha
     *
     * @param float $amount
     * @param bool $showSymbol
     * @return string
     */
    function format_currency($amount, $showSymbol = true)
    {
        $formatted = number_format($amount, 2);
        
        if ($showSymbol) {
            return 'MWK ' . $formatted;
        }
        
        return $formatted;
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     *
     * @return string
     */
    function currency_symbol()
    {
        return 'MWK';
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get currency code
     *
     * @return string
     */
    function currency_code()
    {
        return 'MWK';
    }
}
